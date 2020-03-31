<?php
namespace DotzFramework\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question;

use DotzFramework\Utilities\FileIO;

class UpdateCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'update';

    protected function configure()
    {
	    $this
	        ->setDescription('Update the Dotz Framework.')
	        ->setHelp('Updates the documentation directory.')
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
		            '<info>   	Update Dotz Framework Files</>',
		            '<info>=============================================</>',
		            '<info>=============================================</>',
		            '',
		            '',
		            'Let\'s update the framework\'s documentation.',
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
	 		'<comment>Great! Let\'s update the framework...</comment>',
	 		''
	 	]);

	    $orig = __DIR__ . '/../../../';

	    if($this->moveFiles($path, $orig)){
	    	$output->writeln(['<comment>All done.</comment>']);  
	    }else{
	    	$output->writeln([
		 		'',
		 		'<comment>We could not complete the operation.</comment>',
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

		// remove the old documentation folder and replace...
		exec('rm -rf '.$dest.'/documentation', $o1);

		if(isset($o1) && count($o1) > 0){
			return false;
		}

		exec('cp -rf '.$orig.'/documentation '.$dest.'/', $o2);

		if(isset($o2) && count($o2) > 0){
			return false;
		}

    	return true;
    }
}