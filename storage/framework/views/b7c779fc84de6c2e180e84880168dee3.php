<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subject); ?></title>
</head>
<body>
    <h3>Witaj <?php echo e($user); ?></h3>
    <p> Oto Twoja list składników na <b><?php echo e($meal); ?></b> </p>
    <p> Składniki: </p>
    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p>
            <span> - <?php echo e($product['ingredient']); ?> - <?php echo e($product['count']); ?> <?php echo e($product['metric']); ?> </span>
        </p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <br>
    <p>Miłych przygód kulinarnych</p>
    <p>Well4Work</p>
</body>
</html><?php /**PATH C:\Users\ra4ne\Desktop\jobsite\resources\views/mails/ListMail.blade.php ENDPATH**/ ?>