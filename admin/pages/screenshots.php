<h1>Screenshots</h1>
<style>
    a{
        margin-right: 10px;
    }
    a:last-of-type{
        margin-right: 0px;
    }
</style>
<a href="?p=<?php echo $_GET['type']; ?>s">Go back</a>
<br><br>
<?php
$shots = '';
$screenshots = db_query("SELECT * FROM `screenshots` WHERE `tid`=".prot($_GET['tid'])." AND `type`='".prot($_GET['type'])."'");
if(db_num_rows($screenshots) != 0){
    while ($screenshot = db_fetch_array($screenshots)) {
        $shots .= '<a data-title="'.$screenshot['name'].'" href="'.$screenshot['path'].'" data-lightbox="'.prot($_GET['tid']).'"><img src="'.$screenshot['path'].'" height="200" width="200"></a>';
    }
}
echo $shots;