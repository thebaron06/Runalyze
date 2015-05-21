<?php

namespace Runalyze\plugin\panel\RechenspieleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Runalyze\Calculation\JD\VDOT;
use Runalyze\Calculation\JD\VDOTCorrector;
use Runalyze\Activity\Distance;
use Runalyze\Activity\Duration;
use Runalyze\Calculation\Performance;
use Runalyze\Calculation\Trimp;
use Runalyze\Calculation\Monotony;

class DefaultController extends Controller
{
    /**
    * @Route("/", name="PanelRechenspiele")
    */
    public function indexAction()
    {
        $confData = $this->get('runalyze.conf')->getCategory('data');
        $confTrimp = $this->get('runalyze.conf')->getCategory('trimp');
        $this->get('runalyze.performance.model')->execute();
        
        $TSBmodel = new Performance\TSB(
                $this->get('runalyze.performance.model')->data(),
                $confTrimp['CTL_DAYS'],
                $confTrimp['ATL_DAYS']
        );

        $TSBmodel->calculate();    

        $MonotonyQuery = $this->get('runalyze.performance.model');
        $MonotonyQuery->setRange(time()-(Monotony::DAYS-1)*86400, time());
        $MonotonyQuery->execute();
        $Monotony = new Monotony($MonotonyQuery->data());
        $Monotony->calculate();
                dump($Monotony->valueAsPercentage());
                //dump($Monotony->trainingStrainAsPercentage());
        
        $VDOT        = $confData['VDOT_FORM'];
        $ATLmax      = $confData['MAX_ATL'];
        $CTLmax      = $confData['MAX_CTL'];
        $ModelATLmax = $TSBmodel->maxFatigue();
        $ModelCTLmax = $TSBmodel->maxFitness();
        $ATLabsolute = $TSBmodel->fatigueAt(0);
        $CTLabsolute = $TSBmodel->fitnessAt(0);
        $TSBabsolute = $TSBmodel->performanceAt(0);
        $TrimpValues = array(
                'ATL'		=> round(100*$ATLabsolute/$ATLmax),
                'ATLstring'	=> $confTrimp['TRIMP_MODEL_IN_PERCENT'] ? round(100*$ATLabsolute/$ATLmax) : $ATLabsolute,
                'CTL'		=> round(100*$CTLabsolute/$CTLmax),
                'CTLstring'	=> $confTrimp['TRIMP_MODEL_IN_PERCENT'] ? round(100*$CTLabsolute/$CTLmax) : $CTLabsolute,
                'TSB'		=> round(100*$TSBabsolute/max($ATLabsolute, $CTLabsolute)),
               //'TSBstring'	=> $confTrimp['TSB_IN_PERCENT'] ? sprintf("%+d", round(100*$TSBabsolute/max($ATLabsolute, $CTLabsolute))).'&nbsp;&#37;' : sprintf("%+d", $TSBabsolute),
        );
        $restDays = ceil($TSBmodel->restDays($CTLabsolute, $ATLabsolute));
        $Values = array(
            'vdot'=>array('value' => number_format($VDOT, 2),
                          'bar' => ''),
            'basicEndurance'=> array('value'=> 'test',
                                     'bar' => ''),
            'atl'=> array('value'=> $TrimpValues['ATLstring'],
                        'bar' => $TrimpValues['ATL']),
            'ctl'=> array('value'=> $TrimpValues['CTLstring'],
                'bar' => $TrimpValues['CTL']),
            'tsb'=> array('value'=> 'test',
                'bar' => $TrimpValues['TSB']),
            'restdays'=> array('value'=> $restDays,
                'bar' => ''),
            'easytrimp'=> array('value'=> 'test',
                'bar' => ''),
            'monotony'=> array('value'=> 'test',
                'bar' => ''),
            'trainingstrain'=> array('value'=> round($Monotony->trainingStrain()),
                'bar' => ''),
            'trainingpoints'=> array('value'=> 'test',
                'bar' => ''),
        );
        dump($Values);
        return $this->render('RunalyzePanelRechenspieleBundle::index.html.twig',
                array('values' => $Values));
    }
    
    /**
    * @Route("/info", name="PanelRechenspiele_info")
    */
    public function infoAction()
    {
        return $this->render('RunalyzePanelRechenspieleBundle::info.html.twig');
    }
    
    
    /**
    * @Route("/explanation", name="PanelRechenspiele_explanation")
    */
    public function explanationAction()
    {
        return $this->render('RunalyzePanelRechenspieleBundle::explanation.html.twig');
    }
    
    /**
    * @Route("/form", name="PanelRechenspiele_form")
    */
    public function formAction()
    {
        return $this->render('RunalyzePanelRechenspieleBundle::form.html.twig');
    }
    
    /**
    * @Route("/config", name="PanelRechenspiele_config")
    */
    public function configAction()
    {
        return $this->render('RunalyzePanelRechenspieleBundle::config.html.twig');
    }
}