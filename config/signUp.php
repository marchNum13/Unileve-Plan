<?php
session_start();
error_reporting(0);

// check login status
if($_SESSION['login_uni_lvl'] == true){
    header('Location: dasbor');
    exit();
}

// koneksi database
require_once 'model/conn.php';

// class skema table
require_once 'model/users.php';
require_once 'model/bankAccounts.php';
require_once 'model/recruitingBonuses.php';
require_once 'model/recruitingBonusReports.php';

// panggil class
$users = new users();
$bankAccounts = new bankAccounts();
$recruitingBonuses = new recruitingBonuses();
$recruitingBonusReports = new recruitingBonusReports();

// Generate token jika belum ada
if (empty($_SESSION['csrf_token_uni_lvl'])) {
    $_SESSION['csrf_token_uni_lvl'] = bin2hex(random_bytes(32));
}

// untuk form pada FE
$csrf_token = $_SESSION['csrf_token_uni_lvl'];

// proses post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil data input
    $input_token = $_POST['csrf_token'] ?? '';
    $input_referral = trim(htmlspecialchars(strtoupper($_POST['referral']) ?? ''));
    $input_email = trim(htmlspecialchars(strtolower($_POST['email']) ?? ''));
    $input_password = trim(htmlspecialchars($_POST['password'] ?? ''));
    $input_confirm_password = trim(htmlspecialchars($_POST['confirmPassword'] ?? ''));

    // ambil data session CSRF
    $session_token = $_SESSION['csrf_token_uni_lvl'] ?? '';
    
    // Validasi CSRF
    if (!hash_equals($session_token, $input_token)) {
        header("Location: sign-up.php");
        exit;
    }

    if (
        empty($input_email) || 
        empty($input_password) || 
        empty($input_confirm_password)
    ) {
        $alertError = "Data tidak boleh kosong!";
    } else {
        // inisiasi upline jika ada
        $upline = 'none';
        if (!empty($input_referral)) {
            $checkUpline = $users->read(
                fields: "user_id", 
                key: "referral_code = '$input_referral' AND (is_premium = 'true' AND is_suspen = 'false') LIMIT 1"
            );
            if ($checkUpline['row'] > 0) {
                $upline = $checkUpline['data'][0]['user_id'];
            }else {
                $upline = "NOT-FOUND";
            }
        }

        // validasi code referral
        if ($upline == "NOT-FOUND") {
            $alertError = "Referral code tidak valid!";
        } else {
            // validasi format email
            if (!filter_var($input_email, FILTER_VALIDATE_EMAIL) || strlen($input_email) > 250) {
                $alertError = "Email tidak valid!";
            } else {
                // confirm password
                if ($input_confirm_password != $input_password) {
                    $alertError = "Password tidak sama!";
                } else {
                    // check email terdaftar
                    $checkEmail = $users->read(
                        fields: "email",
                        key: "email = '$input_email' LIMIT 1"
                    );

                    // jika sudah ada
                    if ($checkEmail['row'] > 0) {
                        $alertError = "Email sudah terdaftar!";
                    } else {
                        $user_id = makeUserId();
                        $user_reff = makeReferralCode();
                        $user_name = makeUsername($input_email);

                        $pass_hash = password_hash($input_password, PASSWORD_DEFAULT);

                        $dateNow = round(microtime(true) * 1000);

                        if ($upline == "none") {
                            $createUser = $users->create(
                                fields: "
                                    user_id,
                                    referral_code,
                                    username,
                                    email,
                                    password,
                                    regist_date
                                ",
                                value: "
                                    '$user_id',
                                    '$user_reff'
                                    '$user_name'
                                    '$input_email',
                                    '$pass_hash',
                                    '$dateNow'
                                "
                            );
                        } else {
                            $createUser = $users->create(
                                fields: "
                                    user_id,
                                    referral_code,
                                    username,
                                    email,
                                    password,
                                    user_upline_id,
                                    regist_date
                                ",
                                value: "
                                    '$user_id',
                                    '$user_reff'
                                    '$user_name'
                                    '$input_email',
                                    '$pass_hash',
                                    '$upline',
                                    '$dateNow'
                                "
                            );
                            if ($createUser) {
                                giveBonuses($user_id, $upline);
                            }
                        }

                        if ($createUser) {
                            $createBank = $bankAccounts->create(fields: "user_id", value: "'$user_id'");
                        }
                    }
                }
            }
        }
    }
}

function giveBonuses(string $fromUser, string $upline, int $lvl = 1) {
    global $recruitingBonusReports;

    $bonusAmount = getBonus($lvl);
    $dateNow = round(microtime(true) * 1000);

    if ($bonusAmount == "none") {
        return true;
    } else {
        $create = $recruitingBonusReports->create(
            fields: "
                user_id,
                from_user_id,
                amount,
                lvl,
                date
            ",
            value: "
                '$upline',
                '$fromUser',
                '$bonusAmount',
                '$lvl',
                '$dateNow'
            "
        );
        if ($create) {
            global $users;
            $getUpline = $users->read(
                fields: "user_upline_id",
                key: "user_id = '$upline' LIMIT 1"
            );
            // check jika punya upline
            $uplines = $getUpline['data'][0]['user_upline_id'];
            if (is_null($uplines)) {
                return true;
            } else {
                $checkUplines = $users->read(
                    fields: "user_id", 
                    key: "user_id = '$uplines' AND (is_premium = 'true' AND is_suspen = 'false') LIMIT 1"
                );

                if ($checkUplines['0'] > 0) {
                    return giveBonuses($fromUser, $uplines, $lvl+1);
                } else {
                    return true;
                }
            }
        } 
    }
}
 
function getBonus(int $lvl) {
    global $recruitingBonuses;
    $readBonus = $recruitingBonuses->read(
        fields: "amount",
        key: "lvl = '$lvl' LIMIT 1"
    );

    if ($readBonus['row'] > 0) {
        return $readBonus['data'][0]['amount'];
    } else {
        return "none";
    }
}

function makeUserId() {
    global $users;
    $userId = strtolower(generateCode(6));
    $checkUserId = $users->read(
        fields: "user_id",
        key: "user_id = '$userId' LIMIT 1"
    );

    if ($checkUserId['row'] > 0) {
        return makeUserId();
    } else {
        return $userId;
    }
}

function makeReferralCode() {
    global $users;
    $refCode = generateCode(6);
    $checkRefCode = $users->read(
        fields: "referral_code",
        key: "referral_code = '$refCode' LIMIT 1"
    );

    if ($checkRefCode['row'] > 0) {
        return makeReferralCode();
    } else {
        return $refCode;
    }
}

function makeUsername(string $email) {
    global $users;
    $username = generateUsernameFromEmail($email) . strtolower(generateCode(3));
    $checkUsername = $users->read(
        fields: "username",
        key: "username = '$username' LIMIT 1"
    );

    if ($checkUsername['row'] > 0) {
        return makeUsername($email);
    } else {
        return $username;
    }
}

function generateUsernameFromEmail(string $email) {
    $prefix = explode('@', $email)[0] ?? '';
    $prefix = preg_replace('/[^a-zA-Z0-9]/', '', $prefix);
    return substr($prefix, 0, 6);
}

function generateCode(int $num) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $num; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}