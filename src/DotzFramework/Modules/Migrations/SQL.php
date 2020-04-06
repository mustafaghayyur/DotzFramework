<?php
namespace DotzFramework\Modules\Migrations;

use DotzFramework\Core\Dotz;

/**
 * Helps you create the up() and down() code to be
 * pre-poulated into the migration file.
 */
class SQL {

	
	/**
	 * Mimics Doctrine\Migrations\Generator\SqlGenerator
	 */
	public static function generate( array $sql ) {
        
        $configs = Dotz::config('migrations');
        $code = [];

        foreach ($sql as $query) {
            
            if (stripos($query, $configs->tableName) !== false) {
                continue;
            }

            $code[] = sprintf('$this->addSql(%s);', var_export($query, true));
        }

        return implode("\n", $code);
    }

}