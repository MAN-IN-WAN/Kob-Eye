<?
class codeCheck
{
  function codeCheck(){}
 
  function check($c)
  {
    $sum = 0;
    for ($j=0;$j<5;$j++)
      {
	$sum = $sum + ord($c[$j]);
      }
    $codeA = substr((string)$sum,strlen((string)$sum)-1,1);
    if ($c[5] != $codeA) return 2;
    while (strlen((string)$sum)>1)
      {
	$nsum = 0;
	for ($s=0;$s<strlen((string)$sum);$s++)
	  {
	    $nsum = $nsum + substr((string)$sum,$s,1);
	  }
	$sum=$nsum;
      }
    $codeB = $sum;
    if ($c[6]!= $codeB) return 3;
    return 1;
  }

}

?>