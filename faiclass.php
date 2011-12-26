<?php
class FaiClass {

   var $configspace_dir;
   var $class;
   var $useMeta;
   var $useDiskCommon = true;

   var $classDatas;

   function FaiClass() {}


   function setConfigSpaceDir($configspace_dir) {
      if (! $configspace_dir)
         die ("You must provide the 'configspace_dir' parameter.");

      $this->configspace_dir = $configspace_dir;
   }


   function getConfigSpaceDir() {
      return $this->configspace_dir;
   }


   function parseClass($class) {
      if (! $class)
         die ("You must provide the 'class' parameter.");

      $this->class = $class;
      $this->classDatas['CLASS'] = array();
      $this->classDatas['DISK_CONFIG'] = array();
      $this->classDatas['DEBCONF'] = array();
      $this->classDatas['PACKAGE_CONFIG'] = array();
      $this->classDatas['NETWORK_CONFIG'] = array();

      $this->readClassFile('DEFAULT');
      $this->readClassFile();
      $this->readClassFile('LAST');

      $this->readDiskConfigFile();

      $this->readDebConf();

      $this->readPackageConfig();
   }


   function getClass() {
      return $this->class;
   }


   function readClassFile($class = '', $recurssive = true) {
      if (!$class) $class = $this->class;
                     $this->classDatas['CLASS'][] = $class;

      $file = $this->configspace_dir."/class/".$class;

      print "D: Reading file: $file<br />";
      if (is_file($file)) {
         $lines = file($file);
    
         foreach ($lines as $line) {
            if ( preg_match('/^[^#].*([\S]+).*$/', $line, $matches) ) {
               # Consider only the lines with at least a letter
               $myclasses = split(' ', $matches[0]);
    
               foreach ($myclasses as $myclass) {
                  if ($recurssive) {
                     $this->readClassFile($myclass, $recurssive);
                  } else {
                     #$this->classDatas['CLASS'][] = $myclass;
                  }
               }
            }
         }
      }
   }


   function readDiskConfigFile($dir = 'disk_config', $class = '') {
      if ($this->useDiskCommon && !$class) {
         $this->readDiskConfigFile('disk_common/physical/', 'DEFAULT');
         # TODO
         # $this->classDatas['CLASS']['DISKCOMMON'];
      }

      if (!$class) $class = $this->class;
      $file = $this->configspace_dir."/$dir/".$class;

      print "D: Reading file: $file<br />";
      if (is_file($file)) {
         $lines = file($file);
    
         foreach ($lines as $line) {
            if ( preg_match('/^[^#].*([\S]+).*$/', $line, $matches) ) {
               # Consider only the lines with at least a letter
               $this->classDatas['DISK_CONFIG'][] = $matches[0];
            }
         }
      }
   }


   function readDebConf() {
      foreach ($this->classDatas['CLASS'] as $class) {
         $file = $this->configspace_dir."/debconf/".$class;

         print "D: Reading file: $file<br />";
         if (is_file($file)) {
            $lines = file($file);
    
            foreach ($lines as $line) {
               if ( preg_match('/^[^#].*([\S]+).*$/', $line, $matches) ) {
                  # Consider only the lines with at least a letter
                  $this->classDatas['DEBCONF'][] = $matches[0];
               }
            }
         }
      }
   }


   function readPackageConfig() {
      foreach ($this->classDatas['CLASS'] as $class) {
         $file = $this->configspace_dir."/package_config/".$class;

         print "D: Reading file: $file<br />";
         if (is_file($file)) {
            $lines = file($file);
    
            $section = '';
            foreach ($lines as $line) {
               if ( preg_match('/^[^#].*([\S]+).*$/', $line, $matches) ) {
                  if ( preg_match('/^\s*PACKAGES\s+.*$/', $line, $matchesSection) ) {
                     $section = $matchesSection[0];
                  } else {
                     # Consider only the lines with at least a letter
                     $this->classDatas['PACKAGE_CONFIG'][$class][$section][] = $matches[0];
                  }
               }
            }
         }
      }
   }









}
?>
