<?php

session_start();


$conn = mysqli_connect("localhost","root","","stockbarang");

//menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    //gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name'];//mengambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot));
    $ukuran = $_FILES['file']['size']; 
    $file_tmp = $_FILES['file']['tmp_name'];

    //penamaan file
    $image = md5(uniqid($nama,true) . time()) . '.'.$ekstensi;

    //proses upload gambar
    if(in_array($ekstensi, $allowed_extension) === true){
        //validasi ukuran filenya
        if($ukuran < 15000000){
            move_uploaded_file($file_tmp, 'images/'.$image);

        $addtotable = mysqli_query($conn, "insert into stock (namabarang, deskripsi, stock, image) values('$namabarang','$deskripsi','$stock','$image')");
        if($addtotable){
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        }
        } else {
            //kalau file nya lebih dari 1,5 mb
        echo '
        <script>
            alert("Ukuran terlalu besar");
            window.location.href="index.php";
        </script>
        ';
        }
        } else {
        //kalau file nya tidak jpg/png
        echo '
        <script>
            alert("File harus png/jpg");
            window.location.href="index.php";
        </script>
        ';


    }
};

//menambah barang masuk

if(isset($_POST['barangmasuk'])){
    $barangnya =$_POST['barangnya'];
    $penerima =$_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarangrakha = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$barangnya'");
    $ambildatanyarakha = mysqli_fetch_array($cekstocksekarangrakha);

    $stocksekarangrakha = $ambildatanyarakha['stock'];
    $tambahkanstocksekarangdenganquantityrakha = $stocksekarangrakha+$qty;

    $addtomasukrakha = mysqli_query($conn,"insert into masuk (idbarang, keterangan, qty) values('$barangnya','$penerima','$qty')");
    $updatestockmasukrakha = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantityrakha' where idbarang='$barangnya' ");

    if($addtomasukrakha&&$updatestockmasukrakha){
        header('location:masukrakha.php');
    }else{
        echo 'Gagal';
        header('location:keluarrakha.php');
    }
}

//menambah barang keluar

if(isset($_POST['addbarangkeluar'])){
    $barangnya =$_POST['barangnya'];
    $penerima =$_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstocksekarangrakha = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$barangnya'");
    $ambildatanyarakha = mysqli_fetch_array($cekstocksekarangrakha);

    $stocksekarangrakha = $ambildatanyarakha['stock'];

    if($stocksekarangrakha >= $qty){
        $tambahkanstocksekarangdenganquantityrakha = $stocksekarangrakha-$qty;

        $addtokeluarrakha = mysqli_query($conn,"insert into keluar (idbarang, penerima, qty) values('$barangnya','$penerima','$qty')");
        $updatestockmasukrakha = mysqli_query($conn, "update stock set stock='$tambahkanstocksekarangdenganquantityrakha' where idbarang='$barangnya' ");

        if($addtokeluarrakha&&$updatestockmasukrakha){
            header('location:keluarrakha.php');
        }else{
            echo 'Gagal';
            header('location:keluarrakha.php');
        }   
    }else{
        echo '
        <script>
            alert("Stock saat ini tidak mencukupi");
            window.location.href="keluarrakha.php"; 
        
        </script>
        ';
    }
}

//update barang stock
if(isset($_POST['updatebarang'])){
    $idbarang = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    //gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name'];//mengambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot));
    $ukuran = $_FILES['file']['size']; 
    $file_tmp = $_FILES['file']['tmp_name'];

    //penamaan file
    $image = md5(uniqid($nama,true) . time()) . '.'.$ekstensi;

    if($ukuran==0){
        //jika tidak ingin upload
        $update = mysqli_query($conn,"update stock set namabarang='$namabarang', deskripsi='$deskripsi' where idbarang='$idb'");
        if($update){
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
      }
    } else {
        //jika ingin
        move_uploaded_file($file_tmp, 'images/'.$image);     
        $update = mysqli_query($conn,"update stock set namabarang='$namabarang', deskripsi='$deskripsi', image='$image'where idbarang='$idb'");
        if($update){
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
      }
    }
}

//hps barang 
if(isset($_POST['hapusbarangrakha'])){
    $idbarangrakha = $_POST['idb'];

    $gambar = mysqli_query($conn, "select * from stock where idbarang='$idbarangrakha'");
    $get = mysqli_fetch_array($gambar);
    $img = 'images/'.$get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "delete from stock where idbarang='$idbarangrakha'");
    if($hapus){
        header('location:index.php');
    }else{
        echo 'Gagal';
        header('location:index.php');
    }
};
//edit barang masuk
if(isset($_POST['updatebarangrakhamasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select  * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn,"select * from masuk where idmasuk='$idm' ");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg-$selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update masuk set qty='$qty' , keterangan='$deskripsi' where idmasuk='$idm'");
            if($kurangistocknya&&$updatenya){
                    header('location:masukrakha.php');
                } else {
                    echo 'Gagal';
                    header('location:masukrakha.php');
                }
    } else{
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg-$selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update masuk set qty='$qty' , keterangan='$deskripsi' where idmasuk='$idm'");
            if($kurangistocknya&&$updatenya){
                    header('location:masukrakha.php');
                } else {
                    echo 'Gagal';
                    header('location:masukrakha.php');
                }
            }

}

//menghapus barang masuk

if(isset($_POST['hapusbarangrakhamasuk'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastockrakha = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $datarakha = mysqli_fetch_array($getdatastockrakha);
    $stock = $datarakha['stock'];

    $selisih = $stock-$qty;

    $updaterakha=mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idm'");
    $hapusdatarakha= mysqli_query($conn, "delete from masuk where idmasuk='$idm'");

    if($updaterakha&&$hapusdatarakha){
        header('location:masukrakha.php');
    }else{
        header('location:masukrakha.php');
    }
}

//edit barang keluar
if(isset($_POST['updatebarangrakhakeluar'])){
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select  * from stock where idbarang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $qtyskrg = mysqli_query($conn,"select * from keluar where idkeluar='$idk' ");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if($qty>$qtyskrg){
        $selisih = $qty-$qtyskrg;
        $kurangin = $stockskrg-$selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update keluar set qty='$qty' , penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                    header('location:keluarrakha.php');
                } else {
                    echo 'Gagal';
                    header('location:keluarrakha.php');
                }
    } else{
        $selisih = $qtyskrg-$qty;
        $kurangin = $stockskrg+$selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stock='$kurangin' where idbarang='$idb'");
        $updatenya = mysqli_query($conn,"update keluar   set qty='$qty' , penerima='$penerima' where idkeluar='$idk'");
            if($kurangistocknya&&$updatenya){
                    header('location:keluarrakha.php');
                } else {
                    echo 'Gagal';
                    header('location:keluarrakha.php');
                }
            }

}

//hps barang keluar
if(isset($_POST['hapusbarangrakhakeluar'])){
    $idb = $_POST['idb'];
    $qty = $_POST['kty'];
    $idk = $_POST['idk'];

    $getdatastockrakha = mysqli_query($conn, "select * from stock where idbarang='$idb'");
    $datarakha = mysqli_fetch_array($getdatastockrakha);
    $stock = $datarakha['stock'];

    $selisih = $stock+$qty;

    $updaterakha=mysqli_query($conn,"update stock set stock='$selisih' where idbarang='$idb'");
    $hapusdatarakha= mysqli_query($conn, "delete from keluar where idkeluar='$idk'");

    if($updaterakha&&$hapusdatarakha){
        header('location:keluarrakha.php');
    }else{
        header('location:keluarrakha.php');
    }
}

//menambah admin
if(isset($_POST['addadminrakha'])){
    $emailrakha = $_POST['email'];
    $passwordrakha = $_POST['password'];

    $queryinsert = mysqli_query($conn,"insert into login (email, password) values('$emailrakha','$passwordrakha')");

    if($queryinsert){
        header('location:adminrakha.php');
    }else{
        header('loaction:adminrakha.php');
    }
}

// edit data admin
if(isset($_POST['updateadminrakha'])){
    $emailbarurakha = $_POST['emailadmin'];
    $passwordbaru = $_POST['passwordbaru'];
    $idnya = $_POST['iduser'];

    $queryupdate = mysqli_query($conn,"update login set email='$emailbarurakha', password='$passwordbaru' where iduser='$idnya'");
    if($queryupdate){
        header('location:adminrakha.php');
    }else{
        header('location:adminrakha.php');
    }
}

//hapus data admin
if(isset($_POST['hapusadminrakha'])){
    $id = $_POST['iduser'];

    $querydelete = mysqli_query($conn,"delete from login where iduser='$id'");
    if($querydelete){
        header('location:adminrakha.php');
    }else{
        header('location:adminrakha.php');
    }
}


?>