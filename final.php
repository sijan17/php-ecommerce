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

            $url = "https://uat.esewa.com.np/epay/main";
$data =[
    'amt'=> 100,
    'pdc'=> 0,
    'psc'=> 0,
    'txAmt'=> 0,
    'tAmt'=> $price,
    'pid'=>'ee2c3ca1-696b-4cc5-a6be-2c40d929d453',
    'scd'=> 'EPAYTEST',
    'su'=>'http://merchant.com.np/page/esewa_payment_success?q=su',
    'fu'=>'http://merchant.com.np/page/esewa_payment_failed?q=fu'
];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
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

require 'includes/header.php';
require $nav;?>
<div class="container-fluid product-page">
    <div class="container current-page">
        <nav>
            <div class="nav-wrapper">
                <div class="col s12">
                    <a href="index.php" class="breadcrumb">Home</a>
                    <a href="cart.php" class="breadcrumb">Cart</a>
                    <a href="checkout.php" class="breadcrumb">Checkout</a>
                    <a href="final.php" class="breadcrumb">Thank you</a>
                </div>
            </div>
        </nav>
    </div>
</div>

<div class="container thanks">
    <div class="row">
        <div class="col s12 m3">

        </div>
        

        <div class="col s12 m6">
            <div class="card center-align">
                <div class="card-image">
                    <img src="src/img/thanks.png" class="responsive-img" alt="">
                </div>
                <div class="card-content center-align">
                    <h5>Thank you for your purchase</h5>
                    <p>Your order is on its way Dear : <h5 class="green-text"><?php echo"$firstname_sess". " " . "$lastname_sess";  ?></h5></p>
                </div>
            </div>

            <div class="center-align">
                <a href="details.php" class="button-rounded blue btn waves-effects waves-light">Details</a>
                <a href="index.php" class="button-rounded btn waves-effects waves-light">Home</a>
            </div>
        </div>
        <div class="col s12 m3">

        </div>
    </div>
</div>

<script>

var path="https://uat.esewa.com.np/epay/main";
var params= {
    amt: 100,
    psc: 0,
    pdc: 0,
    txAmt: 0,
    tAmt: 100,
    pid: "ee2c3ca1-696b-4cc5-a6be-2c40d929d453",
    scd: "EPAYTEST",
    su: "http://merchant.com.np/page/esewa_payment_success",
    fu: "http://merchant.com.np/page/esewa_payment_failed"
}

function post(path, params) {
    var form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", path);

    for(var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);
        form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
}

post(path, params)

    </script>

<?php require 'includes/footer.php'; ?>
