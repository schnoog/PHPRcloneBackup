<?php





function DBLoad(){
    global $BU;
    $BU['db'] = [];
    if (file_exists($BU['DBFILE'])){
        $BU['db'] = json_decode(file_get_contents($BU['DBFILE']),true);
    }else{
        DBSave();
    }
}


function DBSave(){
    global $BU;
    file_put_contents($BU['DBFILE'], json_encode($BU['db']));
}



function GetLastChange($dir){

        $cmd= "find '" . $dir . "' -printf '%T@ %p\n'"; //| awk '{print int($1)}";
        $tmp = explode ("\n",`$cmd`);
        $max = [];
        $deb = [];
        foreach($tmp as $ln){
            if(strlen($ln) > 1){
                list($ts,$dump) = explode(".",$ln,2);
                $max[] = $ts;
                $deb[$ts] = $ln;
            }
        }
        $lastchange = max($max);
        //echo "Last change of $dir at $lastchange -- " . $deb[$lastchange] . PHP_EOL;
        return $lastchange;
}


function GetDirsAndTimes($dir,$depth){
    $checkdirs = [];
    $ret = [];
    if($depth > 0){
        $cmd = "find '" . $dir . "' -maxdepth $depth -type d";
        $budirs = explode("\n", `$cmd`);
        foreach($budirs as $budir){
            if(strlen($budir)> 0) {
                if($budir != $dir)    $checkdirs[] = $budir;
            }
        }
    }else{
        $checkdirs = [$dir];
    }

    foreach($checkdirs as $cd){
        //echo "Debug check last change: $cd" . PHP_EOL;
        $ls = GetLastChange($cd);
        $ret[$cd] = $ls; 
    }
    return $ret;
}



//find /your/path -type f -o -type d -printf '%T@ %p\n' | sort -nr | head -n1 | awk '{print int($1)}'