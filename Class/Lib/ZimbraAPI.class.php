<?php
/**
 * Created by PhpStorm.
 * User: mogwaili
 * Date: 28/06/17
 * Time: 11:35
 */

class Zimbra{


    /*
     * Publie la signature sur le compte mail dans BSP
     *
     */
    public function sendSignature($mail,$signature){

        require_once '/var/www/html/Class/Lib/SplClassLoader.php'; // The PSR-0 autoloader from https://gist.github.com/221634
        @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_dump.php';
        @include_once '/var/www/html/Class/Lib/SimpleXmlDebug/simplexml_tree.php';

        $classLoader = new SplClassLoader('Zimbra', realpath('/var/www/html/Class/Lib/')); // Point this to the src folder of the zcs-php repo
        $classLoader->register();


        $servers = array(array('10.0.88.11','hUTHach?p26B'));

        // Define some constants we're going to use
        define('ZIMBRA_PORT', '7071');

        //$batchSize = 10;

        foreach($servers as $server){
            //TODO Verif que les comptes admin sont bien actifs avant !!!


            // Create a new Admin class and authenticate
            $zimbra = new \Zimbra\ZCS\Admin($server[0], ZIMBRA_PORT);
            try{
                $token = $zimbra->auth('zmapi@abtel.link', $server[1]);
                $mailToken = $zimbra->delegateAuth($mail);


                $sigName ='Abtel_auto_Test';
                $alrEx = false;

                $sigs = $zimbra->getSignatures($mail);
                foreach($sigs as $sig){
                    if($sig->get('name') == $sigName) {
                        $zimbra->delSignature($mail,array(
                            'signature'=>array(
                                'name'=>$sigName,
                            )
                        ));
                        break;
                    }
                }
                $sigRes = $zimbra->addSignature($mail,array(
                    'signature'=>array(
                        'name'=>$sigName,
                        '_'=>array('content'=>array(
                            '_'=>$signature
                        ))
                    )
                ));
                echo json_encode(array('succes'=>true,'name'=>$sigRes->get('name'),'id'=>$sigRes->get('id')));


                //$imgName = 'Logo_Abtel_Web';
                //$sigImgType = 'jpg';
                //$tempImg = sys_get_temp_dir().'/'.$imgName.'.'.$sigImgType;
                //$img = '/var/www/html/Logo_Abtel_Web.png';
                //
                //if(is_file($img)){
                //        $img;
                //
                //        $ch = curl_init();
                //        $data = array('filename' => $imgName, 'file' => '@'.$img, 'requestId'=>$mail);
                //        curl_setopt($ch, CURLOPT_URL, "https://$server[0]:".ZIMBRA_PORT."/service/upload?admin=1&fmt=raw"); //,extended
                //
                //        curl_setopt($ch, CURLOPT_POST, 1);
                //        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: ZM_ADMIN_AUTH_TOKEN=".$mailToken));
                //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                //        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                //        //CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
                //        //So next line is required as of php >= 5.6.0
                //        //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                //        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                //        $res = curl_exec($ch);
                //        $err = curl_error($ch);
                //        //$info = curl_getinfo($ch);
                //
                //        curl_close($ch);
                //
                //        //echo '<pre>';
                //        //print_r($info);
                //        //print_r(PHP_EOL);
                //        //print_r($res);
                //        //print_r(PHP_EOL);
                //        //print_r($err);
                //        //echo '</pre>';
                //
                //        $docId = explode(',',$res);
                //        $docId = $docId[2];
                //        $docId = str_replace("'",'',$docId);
                //
                //        $sRes = $zimbra->search($mail,$imgName,'document');
                //
                //
                //        if( $sRes[0]->count() ){
                //                $old['ver'] = (int) $sRes->children()->doc->attributes()->ver;
                //                $old['id'] =  (int) $sRes->children()->doc->attributes()->id;
                //                $old['desc'] = 'Timestamp : '.time();
                //
                //                $zimbra->saveDocument($mail,trim($docId),$old);
                //        } else{
                //                $zimbra->saveDocument($mail,trim($docId));
                //        }
                //}

    //                               $zimbra->modifyPrefs($mail,array('name'=>'zimbraPrefSkin','value'=>'carbon'));

            } catch (Exception $e){
                echo '<pre>';
                print_r ($e);
                echo '</pre>';
                return false;
            }

        }
    }

}