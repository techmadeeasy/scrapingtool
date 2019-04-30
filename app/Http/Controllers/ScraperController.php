<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
ini_set("max_execution_time", 600);
use Goutte\Client;
use Illuminate\Support\Facades\Storage;

class ScraperController extends Controller
{
    public function scrape(){
            $client = new Client();

            //$crawler = Goutte::request('GET', 'http://nicesnippets.com');
            $crawler = $client->request('GET', 'http://www.justice.gov.za/sca/judgments/judgem_sca_2018.html');


            // Get the all a tag under .tablelines href value
            $lists = $crawler->filter('.tablelines a')->each(function ($node) {
                $list[] =  $node->extract(array('href'));
                return $list;
            });
            $desc = $crawler->filter('.tablelines td')->each(function ($node) {
                $des[] =  $node->text();
                return $des;
            });
            $count = 0;
//dd($desc);
            foreach ($lists as $l ){
                foreach ($l as $v){
                    $count++;
                    $good[$count] = $v;
                    $checkifpdf = explode(".", $v[0]);
                    $getname = explode("/", $v[0]); $name = $getname[count($getname)-1]; echo  $name;


                    if($checkifpdf[1]=="pdf"){
                        $file = file_get_contents("http://www.justice.gov.za/sca/judgments/" . $v[0]);
                        $store = Storage::disk("s3")->put("2018/" . $name, $file);
                        if($store){
                            echo "stored" . "<br>";
                        }
                    }
                }
            }

    }

    public function scrapy(){
        $client = new Client();
        $crawler = $client->request('GET', 'http://www.justice.gov.za/sca/judgments/judgem_sca.htm');
        $lists = $crawler->filter('#bodyMain a')->each(function ($node) {
            $list[] =  $node->extract(array('href'));
            return $list;
        });
        $counteven = 1;
       foreach ($lists as $list){
          foreach ($list as $l){
              $counteven++;
              if($counteven % 2==0){
                  $crawler = $client->request('GET', 'http://www.justice.gov.za/sca/judgments/' . $l[0]);
                // echo $l[0]  . $counteven . "<br>";
                //  $lisyear[] = $l[0];

                  // Get the all a tag under .tablelines href value
                  $lists = $crawler->filter('.tablelines a')->each(function ($node) {
                      $list[] =  $node->extract(array('href'));
                      return $list;
                  });
    $count = 0;
    $year = 2018;
                  foreach ($lists as $l ){
                      foreach ($l as $v){
                          $count++;
                          $good[$count] = $v;
                          $checkifpdf = explode(".", $v[0]);
                          $getname = explode("/", $v[0]); $name = $getname[count($getname)-1];


                          if($checkifpdf[1]=="pdf"){
                              $file = file_get_contents("http://www.justice.gov.za/sca/judgments/" . $v[0]);
                              $store = Storage::disk("s3")->put($year . "/" . $name, $file);
                              if($count>3){
                                  echo  $year;
                                  exit;
                              }
                          }
                      }
                  }
              }

          }
       }
    }

    public function scrapytext(){

        $client = new Client();

        //$crawler = Goutte::request('GET', 'http://nicesnippets.com');
        $crawler = $client->request('GET', 'http://www.justice.gov.za/sca/judgments/judgem_sca_2018.html');

        // Get the all a tag under .tablelines href value
//        $lists = $crawler->filter('.tablelines a')->each(function ($node) {
//            $list[] =  $node->extract(array('href'));
//            return $list;
//        });
        $desc = $crawler->filter('.tablelines')->each(function ($node) {
            $des[] =  $node->text();
          // return $des;
            echo  $node->text()."<br>";
        });

//    foreach ($desc as $array){
//        return $array;
//    }

    }
}
