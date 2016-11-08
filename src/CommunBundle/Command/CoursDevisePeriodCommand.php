<?php

namespace CommunBundle\Command;

use CommunBundle\Service\DateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande permettant de récupérer le cours d'une devise
 * Class CoursDeviseCommand
 */
class CoursDevisePeriodCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
            ->setName('cours:get-periode')
            ->addOption('devise', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Devise à récupérer', null)
            ->addOption('duree', null, InputOption::VALUE_OPTIONAL, 'durée de requête (en jour) ', null)
            ->addOption('date', null, InputOption::VALUE_OPTIONAL, 'date de début de requête  ', null)
            ->setDescription('Récupère la devise d\'une commande');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Protection contre une date
        $date = new \DateTime('yesterday');
        if (strlen($input->getOption('date')) > 0) {
            try {
                $date = new \DateTime($input->getOption('date'));
            } catch (\Exception $e) {
                $date = new \DateTime('yesterday');
            }
        }
        $duree     = 30;
        if ($input->getOption('duree') > 0) {
            $duree = $input->getOption('duree');
        }

        $from      = clone $date;
        $from = $from->sub(new \DateInterval('P'.$duree.'D'));
        $listeDate = DateService::createDateRangeArray($from, $date);
        foreach($listeDate as $date) {
            $output->writeln('<info> Calcul de la devise pour '.$date->format('d/m/Y').'</info>');
            $this->getContainer()->get('commun.devise')->recupereEtSauveCours($input->getOption('devise'), $date);
            sleep(2);
        }
    }


}