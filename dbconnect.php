<?
$link = mysqli_connect("localhost", "root", "", "db_image");

function queryRunWithResult($link, $query) {
    $result = mysqli_query($link, $query);
    $data = mysqli_fetch_all ($result, MYSQLI_ASSOC);
    return $data;
}

function queryRun($link, $query) {
    mysqli_query($link, $query);
}
?>