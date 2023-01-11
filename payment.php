<?php
session_start();

if (!isset($_SESSION['logged_in']) && !isset($_POST['pay'])) {
    header('Location: sign.php');
}

if (isset($_POST['pay'])) {

    include 'db.php';

    $querycmd ="SELECT product.id,
                       product.name as 'product',
                       product.price as 'price',

                       command.id as 'idcmd',
                       command.id_product,
                       command.quantity as 'quantity',
                       command.statut,
                       command.id_user as 'iduser',

                       users.id

                       FROM product, command, users
                       WHERE product.id = command.id_product AND users.id = command.id_user
                       AND command.id_user = '{$_SESSION['id']}' AND command.statut = 'ordered'";
    $resultcmd = $connection->query($querycmd);
    if($resultcmd->num_rows > 0){
        while ($rowcmd = $resultcmd->fetch_assoc()) {
            $productcmd = $rowcmd['product'];
            $quantitycmd = $rowcmd['quantity'];
            $pricecmd = $rowcmd['price'];
            $idcmd = $rowcmd['idcmd'];
            $firstnamecmd = $_POST['firstname'];
            $lastnamecmd = $_POST['lastname'];
            $countrycmd = $_POST['country'];
            $citycmd = $_POST['city'];
            $addresscmd = $_POST['address'];

            $idusercmd = $rowcmd['iduser'];


            $price = $pricecmd * $quantitycmd;
            $fullname = $firstnamecmd . " " . $lastnamecmd ;

            $query_details = "INSERT INTO details_command(product,
                                                  quantity,
                                                  price,
                                                  id_command,
                                                  id_user,
                                                  user,
                                                  address,
                                                  country,
                                                  city,
                                                  statut) VALUES('$productcmd',
                                                               '$quantitycmd',
                                                               '$price',
                                                               '$idcmd',
                                                               '$idusercmd',
                                                               '$fullname',
                                                               '$addresscmd',
                                                               '$countrycmd',
                                                               '$citycmd',
                                                               'ready')";
            $resultdetails = $connection->query($query_details);

            $querypay = "UPDATE command SET statut = 'paid' WHERE id_user = '{$_SESSION['id']}' AND statut = 'ordered'";
            $resultpay = mysqli_query($connection, $querypay);
        }
    }
    unset($_SESSION["item"]);

    $nav ='includes/navconnected.php';
    $idsess = $_SESSION['id'];

    $email_sess = $_SESSION['email'];
    $country_sess = $_SESSION['country'];
    $firstname_sess = $_SESSION['firstname'];
    $lastname_sess = $_SESSION['lastname'];
    $city_sess = $_SESSION['city'];
    $address_sess = $_SESSION['address'];
}