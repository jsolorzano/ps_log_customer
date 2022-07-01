<?php
/**
 * 2008-2021 José Solorzano
 *
 * All right is reserved,
 *
 * @author  jsolorzano <solorzano202009@gmail.com>
 */

class LogCustomerHelper
{
    public function installTable()
    {
        $tables = array(
            "sql1" => "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."log_customer` (
                `id_log_customer` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `severity` boolean NOT NULL,
                `error_code` int(11),
                `message` text NOT NULL,
                `object_type` varchar(32),
                `object_id` int(11) unsigned,
                `id_customer` int(11) unsigned,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_log_customer`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8"
        );

        foreach ($tables as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Allows to search the data of a language by iso code.
     * 
     * @return array
     */
    public function getLanguageIso($iso_code)
    {
        return Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.$iso_code.'"');
    }
    
    /**
     * Allows to read the csv file that contains the translations of the module.
     * 
     * @return array
     */
    public function getTranslationsCsv($currentDir, $iso_code)
    {
		if (file_exists($currentDir.'/translations/'.$iso_code.'.csv')) {
		
			$file = fopen($currentDir.'/translations/'.$iso_code.'.csv', 'r');

			$data = array();

			while (($line = fgetcsv($file)) !== FALSE) {
				$data[] = $line;
			}

			fclose($file);
			
		}

        return $data;
    }
    
    /**
     * Allows to install the module translations.
     * 
     * @return boolean
     */
    public function installTranslations($currentDir, $iso_codes)
    {
        foreach ($iso_codes as $iso_code) {
			
			$language = $this->getLanguageIso($iso_code);
			$translations = $this->getTranslationsCsv($currentDir, $iso_code);
			
			if($language['id_lang'] != null && count($translations) > 0){
				
				foreach ($translations as $translation) {
					$insert = array(
						'id_lang' => (int)$language['id_lang'],
						'key' => $translation[2],
						'translation' => $translation[3],
						'domain' => $translation[4]
					);
					
					if (!Db::getInstance()->insert('translation', $insert)) {
						return false;
					}
				}
				
			}
			
        }
        
        return true;
    }
    
    /**
     * Allows to delete the translations of a module.
     * 
     * @return boolean
     */
    public function uninstallTranslations($module)
    {
        if (!Db::getInstance()->delete('translation', "domain LIKE '%".$module."%'")) {
			return false;
		}
        
        return true;
    }
}
