<?php
namespace DotzFramework\Modules\Migrations;

use DotzFramework\Core\Dotz;

/**
 * Create::migration() is useful for modules that wish to
 * make DB schema changes only when the module is enabled.
 *
 * Probably will be used by authentication modules that Dotz
 * packages in the future.
 *
 * We decided to recreate the Doctrine\Migrations\Generator() 
 * code as that library could change causing unecessary disruptions
 * in our processes. 
 */
class Create {

	private static $template = <<< 'TEMPLATE'
<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version<version> extends AbstractMigration
{
    public function getDescription() : string
    {
        return '<description>';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
<up>
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
<down>
    }
}

TEMPLATE;

	public static function generateVersionNumber(){
		return date('YmdHis');
	}

	/**
	 * Mimics Doctrine\Migrations\Generator
	 */
	public static function migration( $version = null, $up = null, $down = null, $description = null ) {
        
        $version = empty($version) ? self::generateVersionNumber() : $version;

        $configs = Dotz::config('migrations');
        
        $placeHolders = [
            '<namespace>',
            '<version>',
            '<description>',
            '<up>',
            '<down>',
        ];

        $replacements = [
            $configs->nameSpace,
            $version,
            $description,
            $up !== null ? '        ' . implode("\n        ", explode("\n", $up)) : null,
            $down !== null ? '        ' . implode("\n        ", explode("\n", $down)) : null,
        ];

        $code = str_replace($placeHolders, $replacements, self::$template);
        $code = preg_replace('/^ +$/m', '', $code);

        $dir = '/'. trim(Dotz::config('app.systemPath'), '/') 
        		.'/'. $configs->migrationsDirectory;

        $path = $dir . '/Version' . $version . '.php';

        file_put_contents($path, $code);

        return $version;
    }

}