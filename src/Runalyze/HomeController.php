<?php
namespace Runalyze;
 
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use \DB;
use \Cache;

function userStat() {
    DB::getInstance()->stopAddingAccountID();
    $stat['user'] = Cache::get('NumUser', 1);
    if ($NumUser == NULL) {
        $stat['user'] = DB::getInstance()->query('SELECT COUNT(*) FROM '.PREFIX.'account WHERE activation_hash = ""')->fetchColumn();
        Cache::set('NumUser', $NumUser, '500', 1);
    }

    $km = Cache::get('NumKm', 1);
    if ($NumKm == NULL) {
        $km= DB::getInstance()->query('SELECT SUM(distance) FROM '.PREFIX.'training')->fetchColumn();
        Cache::set('NumKm', $NumKm, '500', 1);
    }
    $stat['km'] = Activity\Distance::format($km);
    DB::getInstance()->startAddingAccountID();
    $stat['online'] = \SessionAccountHandler::getNumberOfUserOnline();
    return $stat;
}
function cookeInfo() {
    setcookie('CookieInfo', 'true', time()+30*86400);
    return $_COOKIE['CookieInfo'];
}
function switchStart() {
    if($_POST['new_username']) {
        $path = 'register';
        $RegistrationErrors = \AccountHandler::tryToRegisterNewUser();
    } elseif($_POST['username'])
        $path = 'login';
    elseif($_POST['send_username']) {
        $path = 'forgotpw';    
        $forgotpw =   \AccountHandler::sendPasswordLinkTo($_POST['send_username']);
    } else
        $path = 'login';
}
class HomeController
{
    public function loginAction(Application $app)
    {
            $Frontend = new \Frontend(true);
                $stat = userStat();
                $cookieinfo = cookeInfo();
                //Cooke information
                 $path = 'login';
                $response =  $app['twig']->render('login.twig', array(
                    'RUNALYZE_VERSION' => RUNALYZE_VERSION,
                    'numUserOnline' => $stat['online'],
                    'numUser' => $stat['user'],
                    'numKm' => $stat['km'],
                    'errorType' => \SessionAccountHandler::$ErrorType,
                    'cookieInfo' => $cookieinfo,
                    'switchpath' => $path,
                    'forgotpw' => $forgotpw,
                    'USER_CAN_REGISTER' => USER_CAN_REGISTER,
                    'regError' => $RegistrationErrors,
                ));
                return new Response($response);
    }
 
    public function testAction()
    {
        return new Response("Hello");
    }
    
    public function appAction()
    {
        $Frontend = new \Frontend();
        ?> 
        <div id="container">
                <div id="main">
                        <div id="data-browser" class="panel">
                                <div id="data-browser-inner">
                                        <?php
                                        $DataBrowser = new \DataBrowser();
                                        $DataBrowser->display();
                                        ?>
                                </div>
                        </div>

                        <div id="statistics" class="panel">
                                <ul id="statistics-nav">
                                        <?php
                                        $Factory = new \PluginFactory();
                                        $Stats = $Factory->activePlugins( \PluginType::Stat );
                                        foreach ($Stats as $i => $key) {
                                                $Plugin = $Factory->newInstance($key);

                                                if ($Plugin !== false) {
                                                        echo '<li'.($i == 0 ? ' class="active"' : '').'>'.$Plugin->getLink().'</li>';
                                                }
                                        }

                                        if (\PluginStat::hasVariousStats()) {
                                                echo '<li class="with-submenu">';
                                                echo '<a href="#">'.__('Miscellaneous').'</a>';
                                                echo '<ul class="submenu">';

                                                $VariousStats = $Factory->variousPlugins();
                                                foreach ($VariousStats as $key) {
                                                        $Plugin = $Factory->newInstance($key);

                                                        if ($Plugin !== false) {
                                                                echo '<li>'.$Plugin->getLink().'</li>';
                                                        }
                                                }

                                                echo '</ul>';
                                                echo '</li>';
                                        }
                                        ?>
                                </ul>
                                <div id="statistics-inner">
                                        <?php
                                        if (isset($_GET['id'])) {
                                                $Context = new \Context(Request::sendId(), \SessionAccountHandler::getId());
                                                $View = new \TrainingView($Context);
                                                $View->display();
                                        } elseif (isset($_GET['pluginid'])) {
                                                $Factory->newInstanceFor((int)$_GET['pluginid'])->display();
                                        } else {
                                                if (empty($Stats)) {
                                                        echo __('<em>There are no statistics available. Activate a plugin in your configuration.</em>');
                                                } else {
                                                        $Factory->newInstance($Stats[0])->display();
                                                }
                                        }
                                        ?>
                                </div>
                        </div>

                </div>

                <div id="panels">
                        <?php $Frontend->displayPanels(); ?>
                </div>
        </div>
        <?php
        return '';
            }
            
    public function SiteAction(Application $app, $sitename)
    {
        return new Response($app['twig']->render($sitename.'.twig'));
    }
}