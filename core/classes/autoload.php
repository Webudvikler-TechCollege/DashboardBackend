<?php
/**
 * @Description of classloader
 * Uses the Standard PHP Library (SPL) function 
 * spl_autoload_register
 * 
 * @author heka
 */
class AutoLoad {
    private $path2class = __DIR__;
    private $path2app = COREPATH . "userapp";
    private $subdirs = array();
    
    public function __construct() {
        $this->path2app .= substr($this->path2app,-1) != "/" ? "/" : "";
        $this->subdirs[] = $this->path2app;
        $dirs = preg_grep("/^([^.])/", scandir($this->path2app));
        foreach($dirs as $value) {
            if(is_dir($this->path2app . $value)) {
                $this->subdirs[] = $this->path2app . $value . "/";
            }
        }
        
        $this->path2class .= substr($this->path2class,-1) != "/" ? "/" : "";
        $this->subdirs[] = $this->path2class;
        $dirs = preg_grep("/^([^.])/", scandir($this->path2class));
        foreach($dirs as $value) {
            if(is_dir($this->path2class . $value)) {
                $this->subdirs[] = $this->path2class . $value . "/";
            }
        }
        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) {
        
        foreach($this->subdirs as $path) {
            if(file_exists($path . strtolower($className) . ".php")) {
                include_once $path . strtolower($className) . '.php';
            }
        }
    }
}