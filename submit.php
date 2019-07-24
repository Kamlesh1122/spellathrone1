<?php 
include_once __DIR__.'../includes/Helper.php';
//dd($_POST);
if(isset($_POST['action']) && $_POST['action']=='')
{
    $_POST['action']='';
}

if($_POST['action'] == 'signin' && !empty($_POST['password']) && !empty($_POST['username']) ){
    $data = selectOne('SELECT * FROM `tbl_users` WHERE username = ? and password = ? ',[$_POST['username'], md5($_POST['password']) ]);
    if(!empty($data)){
        $_SESSION['customer_id'] = $data['id'];
        redirect(url('/games/index.html?customer_id='.$_SESSION['customer_id']));
    }else{
        flash('error', '  Email or password are wrong.');
        redirect(url('login.php')); 
    }
}
if($_POST['action'] == 'signup' && !empty($_POST['password']) && !empty($_POST['username']) ){
    
    $data = select('SELECT * FROM `tbl_users` WHERE email = ?',[$_POST['username']]);
    if(!empty($data)){
        flash('error', ' Username alraedy registered.');
        redirect(url('signup.php'));
    }

    $data = select('SELECT * FROM `tbl_users` WHERE email = ?',[$_POST['email']]);
    if(!empty($data)){
        flash('error', 'Email alraedy registered.');
        redirect(url('signup.php'));
    }else{    
        $data = [];
        if(isset($_POST['name']) && $_POST['name']=='')
        {
            $_POST['name']='';
        }
        else
        {
            $data['name'] = $_POST['name'];
        }

        if(isset($_POST['email']) && $_POST['email']=='')
        {
            $_POST['email']='';
        }
        else
        {
            $data['email'] = $_POST['email'];
        }

        if(isset($_POST['username']) && $_POST['username']=='')
        {
            $_POST['username']='';
        }
        else
        {
            $data['username'] = $_POST['username'];
        }

        $data['password'] = !empty($_POST['password']) ? md5($_POST['password']) : '';
        $data['created_at'] = date(DATE_DEFAULT);
        $data['updated_at'] = date(DATE_DEFAULT);
        if (insert('tbl_users', $data)) {
            flash('success', ' User registered successfully.');
        } else {
            flash('error', PROCESS_FAIL);
        }
        redirect(url('login.php'));
}
}

if($_POST['action'] == 'addscore'){
    $final = [];
    
    if(!empty($_POST['customer_id']) && !empty($_POST['score'])){
        $data = [];
        
        if(isset($_POST['customer_id']) && $_POST['customer_id']=='')
        {
            $_POST['customer_id']='';
        }
        else
        {
            $data['customer_id'] = $_POST['customer_id'];
        }
        if(isset($_POST['score']) && $_POST['score']=='')
        {
            $_POST['score']='';
        }
        else
        {
            $data['score'] = $_POST['score'];
        }
        $data['created_at'] = date(DATE_DEFAULT);
        $data['updated_at'] = date(DATE_DEFAULT);
        if (insert('tbl_score', $data)) {
            $final = ['msg' => 'success'];
        } else {
            $final = ['msg' => 'fail'];
        }
    }else{
        $final = ['msg' => 'fail'];
    }
    header('Content-Type: application/json');
    echo json_encode($final, JSON_UNESCAPED_UNICODE);
    die;
}
?>