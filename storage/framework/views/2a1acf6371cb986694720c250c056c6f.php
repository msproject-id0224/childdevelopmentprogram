<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__('Verify Your Account')); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .content {
            text-align: center;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #4f46e5;
            margin: 30px 0;
            padding: 20px;
            background-color: #f0fdf4;
            border: 1px dashed #4f46e5;
            border-radius: 8px;
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
        .warning {
            font-size: 14px;
            color: #666666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><?php echo e(__('Child Development Program')); ?></div>
        </div>
        <div class="content">
            <h2><?php echo e(__('Verify Your Account')); ?></h2>
            <p><?php echo e(__('Hello,')); ?></p>
            <p><?php echo e(__('Thank you for registering/logging in. Use the following OTP code to verify your identity:')); ?></p>
            
            <div class="otp-code"><?php echo e($code); ?></div>
            
            <p class="warning"><?php echo e(__('This code will expire in 5 minutes.')); ?></p>
            <p><?php echo e(__('If you did not request this code, please ignore this email. Do not share this code with anyone.')); ?></p>
        </div>
        <div class="footer">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(__('Child Development Program')); ?>. <?php echo e(__('All rights reserved.')); ?></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Administrator\OneDrive\Documents\code_pr\child-development-program\resources\views/emails/otp.blade.php ENDPATH**/ ?>