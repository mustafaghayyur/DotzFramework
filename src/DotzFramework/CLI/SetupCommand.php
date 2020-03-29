<?php
namespace DotzFramework\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question;

use DotzFramework\Utilities\FileIO;

class SetupCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'setup';

    protected function configure()
    {
	    $this
	        ->setDescription('Setup the Dotz Framework.')
	        ->setHelp('This operation will ask you some questions along the setup process.')
			;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	    $helper = $this->getHelper('question');

		$output->writeln([
		            '',
		            '',
		            '<info>=============================================</>',
		            '<info>=============================================</>',
		            '<info>   		Dotz Framework</>',
		            '<info>=============================================</>',
		            '<info>=============================================</>',
		            '',
		            '',
		            'Let\'s setup the framework\'s working files.',
		            '',
		            '<question>You can exit this app by holding the', 
		            '[ctrl] + [c] together</question>',
		            ''
		        ]);

		$dir = trim(__DIR__, '/');
		$a = explode('/', $dir);
		array_pop($a);
		array_pop($a);
		array_pop($a);
		array_pop($a);
		array_pop($a);
		array_pop($a);
		$dir = '/' . implode('/', $a);

		$output->writeln([
	    	'',
	    	'=============================================',
	    	'<comment>APP SYSTEM PATH:</comment>',
	    	'<comment>'.$dir.'</comment>',
			'=============================================',
	    	''
	    	]);

	    $q1 = new Question\Question('<comment>Is this your app\'s root directory [y/n]: </comment>');
        $q1->setMaxAttempts(null);
	    $dirOk = $helper->ask($input, $output, $q1);

	    if(strtolower(substr($dirOk, 0, 1)) == 'y'){

	    	$path = $dir;

	    }else{

	    	//recurring function to get the right path.
	    	$path = $this->askForPath($input, $output, $helper);
	    }

	    $output->writeln([
	 		'',
	 		'<comment>Great! Let\'s setup the framework...</comment>',
	 		''
	 	]);

	    $orig = __DIR__ . '/../../../';

	    if($this->moveFiles($path, $orig)){
	    	$output->writeln(['<comment>All done.</comment>']);  
	    }else{
	    	$output->writeln([
		 		'',
		 		'<comment>We could not move all the files.</comment>',
		 		'<comment>Please check permissions and try running this command again.</comment>',
		 		''
		 	]);  
	    }
    }

    public function askForPath($input, $output, $helper){
    	$q2 = new Question\Question('<comment>Please enter the full system path for your application root:');
        $q2->setMaxAttempts(null);
	    $path = $helper->ask($input, $output, $q2);

	    if(file_exists($path)){
	    	
	    	$f = new FileIO($path.'/test.txt', 'w+');
	    	
	    	if($f->ok){

	    		unlink($path.'/test.txt');
	    		return $path;
	    	}
	    }

	    $output->writeln(['',
	 		'<comment>Path not found or not writable. Try again:</comment>',
	 		'']);

		return $this->askForPath($input, $output, $helper);
    }

    public function moveFiles($dest, $orig){

		if(copy($orig.'/modules.php', $dest.'/modules.php')){
			if(copy($orig.'/migrations.php', $dest.'/migrations.php')){
				if(copy($orig.'/migrations-db.php', $dest.'/migrations-db.php')){
					if(copy($orig.'/index.php', $dest.'/index.php')){
						if(copy($orig.'/.htaccess', $dest.'/.htaccess')){
					    	
							//Five copy commands were successful, therefore
							//it is safe to use command line operations to copy 
							//over the three directories below:
							
							exec('cp -rf '.$orig.'/configs '.$dest.'/', $o1);

							if(isset($o1) && count($o1) > 0){
								return false;
							}

							exec('cp -rf '.$orig.'/migrations '.$dest.'/', $o2);

							if(isset($o2) && count($o2) > 0){
								return false;
							}

							exec('cp -rf '.$orig.'/documentation '.$dest.'/', $o3);

							if(isset($o3) && count($o3) > 0){
								return false;
							}

							if(!file_exists($dest.'/src')){
								mkdir($dest.'/src');
							}

							exec('cp -rf '.$orig.'/src/App '.$dest.'/src/', $o4);

							if(isset($o4) && count($o4) > 0){
								return false;
							}

							//Update the composer.json file to auto-load modules.php
					    	$json = json_decode(file_get_contents($dest.'/composer.json'));
							unset($json->autoload);

							$json->autoload = [
								'psr-4' => [ 
									''=>'src/' 
								],
								'files' => [
									'modules.php'
								]
							];

							$ok = file_put_contents(
								$dest.'/composer.json', 
								json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
							);
					    	
					    	if($ok !== false){
					    		//success..
					    		return true;
					    	}
						}
					}
				}
			}
		}

    	return false;
    }
}