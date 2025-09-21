<?php

$BD = __DIR__;
require_once($BD . "/config.php");
require_once($BD . "/functions.php");


DBLoad();
BackupPrepare();


function BackupPrepare($dir = ""){
    global $BU;
    if($dir != ""){ 
        $work = $BU['DIRS'][$dir];
        BackupLaunch($work);
    }else{
        foreach($BU['DIRS'] as $key => $work){
            BackupLaunch($work);
        }
    }
}

function BackupLaunch($WORKDIR){
    global $BU;
    
    $dir = $WORKDIR['dir'];
    $paras = $WORKDIR['paras'];

    $sublevel = $WORKDIR['sublevel'];
    $depth = $sublevel + 0;
    $precmd = '';
    if(isset($WORKDIR['pre-cmd'])) $precmd = $WORKDIR['pre-cmd'];

    $aftcmd = '';
    if(isset($WORKDIR['aft-cmd'])) $aftcmd = $WORKDIR['aft-cmd'];
    

    $subdirs = GetDirsAndTimes($dir,$depth);
    foreach($subdirs as $subdir => $lastchange){
        //echo $subdir . " --- " . $lastchange . PHP_EOL;

        $BU['db'][$subdir]['lastchange'] = $lastchange;
        if(isset($BU['db'][$subdir]['lastbackup'])){
            $lastbackup = $BU['db'][$subdir]['lastbackup'];
        }else{
            $lastbackup = 0;
        }
        $wd = $lastbackup + $BU['common']['time_tolerance'];

        if($lastchange > $wd){ // Perform backup


            echo "Backing up "  . $subdir . PHP_EOL;

            foreach($BU['TARGETS'] as $tname => $target){
                echo "-- on " .  $tname . PHP_EOL; 
                if(strlen($precmd)> 0) {
                    $xx = `$precmd`;
                   // echo "precommand-result:" . $xx . PHP_EOL;
                }
                $command = "rclone  " . $BU['MODE']. " " . $BU['VERBOSITY'] ." " . implode(" ",$target['paras']). " ". implode(" ",$paras) . " '" . $subdir  . "' '" . $target['dir'] . $subdir  . "'";
                $BU['db'][$subdir]['lastbackup'] = time();
                //echo $command . PHP_EOL;
                echo `$command`;
                DBSave();
                if(strlen($aftcmd)> 0){
                     $xx = `$aftcmd`;
                  //   echo "aftcommand-result:" . $xx . PHP_EOL;
                }
                echo "-- on $tname done" . PHP_EOL;

            }
        }
    }
    DBSave();
}



