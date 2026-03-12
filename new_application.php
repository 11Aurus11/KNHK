<?php
// Файл: new_application.php
session_start();
$page_title = 'Новая заявка - Корочки.есть';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'php/application_functions.php';

$payment_methods = getPaymentMethods();
$error = $_SESSION['application_error'] ?? '';
$old_data = $_SESSION['old_application_data'] ?? [];

unset($_SESSION['application_error']);
unset($_SESSION['old_application_data']);

include 'includes/header.php';
?>
<html>
    <head>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h1 class="text-center mb-4">Создание новой заявки</h1>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form action="application_handler.php" method="POST">
                    <div class="form-group">
                        <label for="course_name">Наименование курса</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" required 
                            placeholder="Введите название курса"
                            value="<?php echo htmlspecialchars($old_data['course_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="start_date">Желаемая дата начала обучения</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required 
                            value="<?php echo htmlspecialchars($old_data['start_date'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Способ оплаты</label>
                        <div class="radio-group">
                            <?php foreach ($payment_methods as $method): ?>
                                <div class="radio-option">
                                    <input type="radio" id="payment_<?php echo $method['id']; ?>" 
                                        name="payment_method" value="<?php echo $method['id']; ?>" required
                                        <?php echo (isset($old_data['payment_method']) && $old_data['payment_method'] == $method['id']) ? 'checked' : ''; ?>>
                                    <label for="payment_<?php echo $method['id']; ?>">
                                        <?php echo htmlspecialchars($method['name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Отправить заявку
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="my_applications.php">Вернуться к списку моих заявок</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>