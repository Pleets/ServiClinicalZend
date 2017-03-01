<?php
/**
 * ServiClinical (http://www.serviclinical.com)
 *
 * @link      http://github.com/Pleets/ServiClinicalZend
 * @copyright Copyright (c) 2017 ServiClinical. (http://www.serviclinical.com)
 * @license   http://www.serviclinical.com/license
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Auth\Model\AclAdapter;

class IndexController extends AbstractActionController
{
    private $userTable;
    private $acl;

    private function authentication()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {

            $terminal = ($xmlHttpRequest = $this->getRequest()->isXmlHttpRequest()) ? "xmlHttpRequest" : "attemp";

            return $this->redirect()->toRoute('auth',
                array('action' => 'login', 'id' => $terminal)
            );
        }
    }

    private function forceAuthentication()
    {
        $auth = new AuthenticationService();
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        if (!$auth->hasIdentity() && $xmlHttpRequest)
            exit;
    }

    private function authenticate()
    {
        $this->authentication();
        $this->forceAuthentication();
    }

    private function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Auth\Model\Entity\UserTable');
        }
        return $this->userTable;
    }

    private function getIdentity()
    {
        $auth = new AuthenticationService();
        return $auth->getIdentity();
    }

    private function configureAcl()
    {
        $acl = new AclAdapter($this);
        $acl->parseRol($this->getUserTable()->getPermission($this->getIdentity()->cod_usu));
        $this->acl = $acl;
        return $acl;
    }

    private function isAllow($resource)
    {
        $acl = new AclAdapter($this);
        $acl->parseRol($this->getUserTable()->getPermission($this->getIdentity()->cod_usu));
        return $acl->isAllowed($resource);
    }

    public function count($file)
    {
        $file = fopen ($file, "r");

        $num_lines = 0;
        $characters = 0;

        while (!feof ($file)) {
            if ($line = fgets($file)){
               $num_lines++;
               $characters += strlen($line);
            }
        }
        fclose ($file);

        return array(
            'lines' => $num_lines,
            'characters' => $characters,
        );
    }

    public function countDirs($path)
    {
        $num_dirs = 0;
        if (is_dir($path))
        {
            if ($dh = opendir($path))
            {
                while (($file = readdir($dh)) !== false)
                {
                    $_file = $path."/".$file;
                    if (is_dir($_file) && $file!="." && $file!="..")
                        $num_dirs++;
                }
                closedir($dh);
            }
        }
        return $num_dirs;
    }

    public function countFiles($path)
    {
        $num_files = 0;
        if (is_dir($path))
        {
            if ($dh = opendir($path))
            {
                while (($file = readdir($dh)) !== false)
                {
                    $_file = $path."/".$file;
                    if (is_file($_file) && $file!="." && $file!="..")
                        $num_files++;
                }
                closedir($dh);
            }
        }
        return $num_files;
    }

    public function ls($path)
    {
        if (is_dir($path))
        {
            if ($dh = opendir($path))
            {
                while (($file = readdir($dh)) !== false)
                {
                    $_file = $path."/".$file;

                    if (basename($_file) == 'language')
                        continue;

                    $module = (basename(dirname($_file)) == 'module') ? "<span class='label label-info'>module</span>" : "<span class='label label-default'>folder</span>";

                    if ($file!="." && $file!=".." && is_dir($_file)) {
                        $dirs = $this->countDirs($_file);
                        $files = $this->countFiles($_file);
                        echo "<tr class='success'><td>$path/$file $module</td><td><span class='label label-primary'>$dirs dirs</span></td><td><span class='label label-primary'>$files files</span></td></tr>";
                    }

                    echo "<tr class='warning'>";
                    if (is_file($_file)) {
                        $data = $this->count($_file);

                        // Total count
                        $_SESSION["buffer"]["lines"] += $data["lines"];
                        $_SESSION["buffer"]["characters"] += $data["characters"];

                        echo "<td>$_file</td><td><span class='label label-danger'>".$data["lines"]." lines</span></td><td><span class='label label-warning'>".$data["characters"]." characters</span></td>";
                    }
                    echo "</tr>";
                    if (is_dir($_file) && $file!="." && $file!="..")
                        $this->ls($path."/".$file);
                }
                closedir($dh);
            }
            else
                throw new \Exception("No se puedo abrir el directorio $path");
        }
    }

    public function indexAction()
    {
        $auth = new AuthenticationService();
        $model_data = array();
        $model_data["identity"] = ($auth->hasIdentity()) ? true : false;
        if ($model_data["identity"])
            $model_data["acl"] = $this->configureAcl();
        return new ViewModel($model_data);
    }

    public function sitemapAction()
    {
    	return new ViewModel();
    }

    public function developmentAction()
    {
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        try {
            if (!$this->isAllow("viewPermissions"))
                throw new \Exception("No tienes permiso para ver el desarrollo del sistema");
        } catch (\Exception $e) {
            $view = new ViewModel(array('Exception' => $e->getMessage()));
            return $view;
        }

        return new ViewModel(array(
            'controller' => $this,
            'modules' => dirname(dirname(dirname(dirname(__DIR__)))),
            'js' => dirname(dirname(dirname(dirname(dirname(__DIR__)))))."/public/js/application",
            'css' => dirname(dirname(dirname(dirname(dirname(__DIR__)))))."/public/css/application",
            'config' => dirname(dirname(dirname(dirname(dirname(__DIR__)))))."/config",
        ));
    }

    public function dbcopyAction()
    {
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        return new ViewModel();
    }

    public function createdbcopyAction()
    {
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        $model_data = array();

        $file = "./public/data/db_backup_".date("Y_m_d_H_i_s").".sql";

        try {
            exec("mysqldump --user=hcneuro_mcadiz --password=xK63rTuXguyd --compress hcneuro_mcadiz --force > " . $file);
        }
        catch (\Exception $e) {
            $model_data['Exception'] = $e->getMessage();
            $view = new ViewModel($model_data);
            //if ($xmlHttpRequest)
                $view->setTerminal(true);
            return $view;
        }

        if (file_exists($file))
        {
            $model_data["size"] = (int) (filesize($file) / 1024);
            $model_data["name"] = $file;
        }

        $model = new ViewModel( array( 'response' => $model_data ) );
        $model->setTerminal(true);
        return $model;
    }

    public function viewcopiesAction()
    {
        $xmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $this->authenticate();

        $model_data = array();

        $path = "./public/data/";

        $model = new ViewModel( array( 'response' => $model_data ) );
        $model->setTerminal(true);
        return $model;
    }

}
