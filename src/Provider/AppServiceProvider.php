<?php

declare(strict_types=1);

namespace App\Provider;

use App\Command\TelegramLogoutCommand;
use danog\MadelineProto\Logger as MadelineProtoLogger;
use danog\MadelineProto\Settings;
use Override;
use App\Framework\Console\Console;
use App\Framework\Console\ConsoleInterface;
use App\Framework\Database\DbConnectionBuilder;
use App\Framework\Database\QueryBuilder;
use App\Framework\Http\Request;
use App\Framework\Http\ServerRequestInterface;
use App\Framework\Provider\AbstractServiceProvider;
use App\Model\Contract\Repository\Balance\BalanceRepositoryInterface;
use App\Model\Contract\Repository\Balance\CachedBalanceRepository;
use App\Model\Contract\Repository\Contract\CachedContractRepository;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\Model\Contract\Repository\ContractService\CachedContractServiceRepository;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Repository\InsuredPerson\CachedInsuredPersonRepository;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Contract\Repository\ProvidedService\CachedProvidedServiceRepository;
use App\Model\Contract\Repository\ProvidedService\ProvidedServiceRepositoryInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Repository\CachedInsuranceCompanyRepository;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use App\Model\Person\Repository\CachedPersonRepository;
use App\Model\Person\Repository\PersonRepositoryInterface;
use App\Model\Service\Repository\CachedServiceRepository;
use App\Model\Service\Repository\ServiceRepositoryInterface;
use App\ReadModel\Contract\Balance\BalanceFetcherInterface;
use App\ReadModel\Contract\Balance\CachedBalanceFetcher;
use App\ReadModel\Contract\Contract\CachedContractFetcher;
use App\ReadModel\Contract\Contract\ContractFetcherInterface;
use App\ReadModel\Contract\ContractService\CachedContractServiceFetcher;
use App\ReadModel\Contract\ContractService\ContractServiceFetcherInterface;
use App\ReadModel\Contract\Debtor\CachedDebtorFetcher;
use App\ReadModel\Contract\Debtor\DebtorFetcherInterface;
use App\ReadModel\Contract\InsuredPerson\CachedInsuredPersonFetcher;
use App\ReadModel\Contract\InsuredPerson\InsuredPersonFetcherInterface;
use App\ReadModel\Person\CachedPersonFetcher;
use App\ReadModel\Person\PersonFetcherInterface;
use App\ReadModel\ProvidedService\CachedProvidedServiceFetcher;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;
use App\ReadModel\Service\CachedServiceFetcher;
use App\ReadModel\Service\ServiceFetcherInterface;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Redis\RedisCacheItemPool;
use App\Service\Cache\Redis\RedisClientBuilder;
use App\Event\Dispatcher\EventDispatcher;
use App\Event\Dispatcher\EventDispatcherInterface;
use App\Event\Dispatcher\ListenerProvider;
use App\Event\Dispatcher\ListenerProviderInterface;
use App\Service\Hydrator\Hydrator;
use App\Service\Hydrator\HydratorInterface;
use App\Service\LoggerActivity\ActorId;
use App\Service\LoggerActivity\DbLoggerActivity;
use App\Service\LoggerActivity\LoggerActivityInterface;
use App\Service\Queue\QueueClientInterface;
use App\Service\Queue\RabbitMQ\RabbitMQQueueClient;
use App\Service\Sender\Mail\MailSender;
use App\Service\Sender\SenderInterface;
use danog\MadelineProto\API;
use PDO;
use Redis;

/**
 * @psalm-api
 */
class AppServiceProvider extends AbstractServiceProvider
{
    protected array $bindings = [
        ServerRequestInterface::class              => Request::class,
        ConsoleInterface::class                    => Console::class,
        HydratorInterface::class                   => Hydrator::class,
        QueueClientInterface::class                => RabbitMQQueueClient::class,
        SenderInterface::class                     => MailSender::class,
        ContractRepositoryInterface::class         => CachedContractRepository::class,
        InsuranceCompanyRepositoryInterface::class => CachedInsuranceCompanyRepository::class,
        PersonRepositoryInterface::class           => CachedPersonRepository::class,
        ServiceRepositoryInterface::class          => CachedServiceRepository::class,
        InsuredPersonRepositoryInterface::class    => CachedInsuredPersonRepository::class,
        ContractServiceRepositoryInterface::class  => CachedContractServiceRepository::class,
        ProvidedServiceRepositoryInterface::class  => CachedProvidedServiceRepository::class,
        ProvidedServiceFetcherInterface::class     => CachedProvidedServiceFetcher::class,
        BalanceRepositoryInterface::class          => CachedBalanceRepository::class,
        BalanceFetcherInterface::class             => CachedBalanceFetcher::class,
        DebtorFetcherInterface::class              => CachedDebtorFetcher::class,
        ServiceFetcherInterface::class             => CachedServiceFetcher::class,
        PersonFetcherInterface::class              => CachedPersonFetcher::class,
        ContractFetcherInterface::class            => CachedContractFetcher::class,
        ContractServiceFetcherInterface::class     => CachedContractServiceFetcher::class,
        InsuredPersonFetcherInterface::class       => CachedInsuredPersonFetcher::class,
        CacheItemPoolInterface::class              => RedisCacheItemPool::class,
        ListenerProviderInterface::class           => ListenerProvider::class,
        EventDispatcherInterface::class            => EventDispatcher::class,
    ];

    #[Override]
    protected function addMoreBindings(): void
    {
        $this->addRedisConnection();
        $this->addBindDbConnection();
        $this->addBindMadelineProtoApi();
        $this->addLoggerActivity();

        $this->bindings[TelegramLogoutCommand::class] = fn() => (new TelegramLogoutCommand(getenv('PATH_TO_MADELINE_SESSION')));
    }

    private function addBindDbConnection(): void
    {
        $this->bindings[PDO::class] = fn() => (new DbConnectionBuilder())
            ->setDriver(getenv('DB_DRIVER'))
            ->setHost(getenv('DB_HOST'))
            ->setPort(intval(getenv('DB_PORT')))
            ->setDbName(getenv('DB_NAME'))
            ->setUserName(getenv('DB_USERNAME'))
            ->setPassword(getenv('DB_PASSWORD'))
            ->build();
    }

    private function addBindMadelineProtoApi(): void
    {
        $appInfo = (new Settings\AppInfo())
            ->setApiId((int) getenv('TELEGRAM_APP_API_ID'))
            ->setApiHash(getenv('TELEGRAM_APP_API_HASH'));
        $logger = (new Settings\Logger())
            ->setType(MadelineProtoLogger::FILE_LOGGER)
            ->setExtra(getenv('PATH_TO_MADELINE_LOG') . '/log.log');
        $settings = (new Settings())->setAppInfo($appInfo)->setLogger($logger);

        $this->bindings[API::class] = fn() => new API(
            getenv('PATH_TO_MADELINE_SESSION'),
            $settings
        );
    }

    private function addRedisConnection(): void
    {
        $this->bindings[Redis::class] = fn() => (new RedisClientBuilder())
            ->setHost(getenv('REDIS_HOST'))
            ->setPort(intval(getenv('REDIS_PORT')))
            ->setDbIndex(intval(getenv('REDIS_DB_INDEX')))
            ->setPassword(getenv('REDIS_PASSWORD'))
            ->build();
    }

    private function addLoggerActivity(): void
    {
        $this->bindings[LoggerActivityInterface::class] = function () {
            $insuranceCompanyId = $this->container->get(InsuranceCompanyId::class);
            $actorId = new ActorId($insuranceCompanyId->getValue());

            /* @var QueryBuilder $queryBuilder */
            $queryBuilder = $this->container->get(QueryBuilder::class);

            return new DbLoggerActivity($actorId, $queryBuilder);
        };
    }
}
