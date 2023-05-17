<?php

namespace App\Command;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Service\ApiNbpCurrencyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:currency-get',
    description: 'Add a short description for your command',
)]
class CurrencyGetCommand extends Command
{
    public function __construct(private ApiNbpCurrencyService $currencyService, private CurrencyRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('code', InputArgument::OPTIONAL, 'Currency code to retrieve');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = $input->getArgument('code');

        $data = is_null($code) ? $this->currencyService->getCurrencies() : $this->currencyService->getCurrencyByCode($code);

        foreach ($data['list'] as $item) {
            $currency = $this->repository->findByCode($item['code']);
            if (is_null($currency)) {
                $currency = (new Currency())
                    ->setCurrencyCode($item['code'])
                    ->setName($item['currency']);
            }

            $currency
                ->setEffectiveDate(new \DateTime($data['effectiveDate']))
                ->setExchangeRate($item['mid'])
                ->setUpdateDate(new \DateTime('now'));
            $this->repository->save($currency);
        }
        $this->repository->flush();

        $io->success('Finished!');
        return Command::SUCCESS;
    }
}
