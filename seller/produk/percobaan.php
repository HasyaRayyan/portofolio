
<html>
 <head>
  <title>
   Order Summary
  </title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
   body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 20px;
        }
        .section-header {
            font-size: 18px;
            font-weight: bold;
            color: #ff5722;
            display: flex;
            align-items: center;
        }
        .section-header i {
            margin-right: 10px;
        }
        .address, .product, .shipping {
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fff;
        }
        .address p, .product p, .shipping p {
            margin: 5px 0;
        }
        .address .change {
            color: #007bff;
            cursor: pointer;
        }
        .product-header, .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-header {
            font-weight: bold;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .product-item img {
            width: 100px;
            height: auto;
            margin-right: 20px;
        }
        .product-item .details {
            flex: 1;
        }
        .product-item .details p {
            margin: 0;
        }
        .shipping .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .shipping .total {
            font-weight: bold;
            color: #ff5722;
        }
        .chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff5722;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .chat-button i {
            margin-right: 10px;
        }
        .product-header span {
            margin-right: 20px;
        }
        .product-item .price, .product-item .quantity, .product-item .subtotal {
            width: 100px;
            text-align: center;
            
        }
        .quantity{
            mar
        }
        .product-item .price {
            margin-left: auto;
            margin-right: 500px;
        }
        .product-item .price, .product-header .price {
            text-align: center;
            width: 100px;
        }
  </style>
 </head>
 <body>
  <div class="container">
   <div class="section address">
    <div class="section-header">
     <i class="fas fa-map-marker-alt">
     </i>
     Alamat Pengiriman
    </div>
    <p>
     Hasya Rayyan Bahaudin Mahardik (+62) 89636839658
    </p>
    <p>
     Jalan Rohjoyo, RT.5/RW.5, Dusun Banaran, Bumiaji (Rumah No 12), KOTA BATU - BUMIAJI, JAWA TIMUR, ID 65331
    </p>
    <span class="change">
     Ubah
    </span>
   </div>
   <div class="section product">
    <div class="section-header">
     Produk Dipesan
    </div>
    <div class="product-header">
     <span>
      Nama Barang
     </span>
     <span class="price">
      Harga
     </span>
     <span class="quantity">
      Jumlah
     </span>
     <span class="subtotal">
      Subtotal
     </span>
    </div>
    <div class="product-item">
     <div class="details">
      <p>
       <img alt="Image of a monitor stand" height="100" src="https://storage.googleapis.com/a1aa/image/nraROy0SbIbLIJyjK4yeLeaRtfedof6BVy5D5U9fLby39yD8E.jpg" width="100"/>
      </p>
     </div>
     <span class="price">
      Rp50.000
     </span>
     <span class="quantity">
      2
     </span>
     <span class="subtotal">
      Rp100.000
     </span>
    </div>
   </div>
   <div class="section shipping">
    <div class="total">
     <span>
      Ongkir:
     </span>
     <span>
      Rp16.000
     </span>
    </div>
    <div class="total">
     <span>
      Bea Cukai:
     </span>
     <span>
      Rp500
     </span>
    </div>
    <div class="total">
     <span>
      Total Pesanan (1 Produk):
     </span>
     <span>
      Rp116.500
     </span>
    </div>
   </div>
  </div>
  <div class="chat-button">
   <i class="fas fa-comments">
   </i>
   Chat
  </div>
 </body>
</html>