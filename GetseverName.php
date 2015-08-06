<form action="GetseverName.php" method="post">
  Enter Url <input type="text" name="geturl">
  <input type="submit" value="Submit">
</form>
<?php
$url = $_POST['geturl'];
$ch = curl_init($url);
//curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
$headers = get_headers_from_curl_response($response);
extract($headers);


function get_headers_from_curl_response($response)
{
    $headers = array();

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else
        {
            list ($key, $value) = explode(': ', $line);

            $headers[$key] = $value;
        }

    return $headers;
}


?>
<table border="1">
    <tr><td>&nbsp;&nbsp;Server Name::&nbsp;&nbsp;&nbsp; </td><td>&nbsp;&nbsp; <?php if($Server ==''){echo 'Please Fill Url Show OutPut';}else {echo  $Server;} ?>&nbsp;&nbsp;</td></tr></table>
