<?php
// Файл: my_applications.php
session_start();
$page_title = 'Мои заявки - Корочки.есть';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'php/application_functions.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'user';
$applications = getUserApplications($user_id);
$success = $_SESSION['application_success'] ?? '';
$error = $_SESSION['application_error'] ?? '';

unset($_SESSION['application_success']);
unset($_SESSION['application_error']);

include 'includes/header.php';
?>

<?php if ($user_role === 'admin'): ?>
    <div class="mb-3">
        <a href="admin/dashboard.php" class="btn btn-danger">
            <i class="fas fa-cog"></i> Перейти в панель администратора
        </a>
    </div>
<?php endif; ?>

<h1 class="mb-4">Мои заявки на обучение</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
<html>
    <head>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <?php if (empty($applications)): ?>
            <div class="card text-center">
                <div class="card-body">
                    <p class="mb-3">У вас пока нет ни одной заявки на обучение</p>
                    <a href="new_application.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать первую заявку
                    </a>
                </div>
            </div>
    <?php else: ?>
<html>
    <head>
        <link rel="stylesheet" href="./css/style.css">
    </head>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Курс</th>
                        <th>Дата начала</th>
                        <th>Способ оплаты</th>
                        <th>Статус</th>
                        <th>Дата подачи</th>
                        <th>Отзыв</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>#<?php echo $app['id']; ?></td>
                            <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($app['desired_start_date'])); ?></td>
                            <td><?php echo htmlspecialchars($app['payment_method_name']); ?></td>
                            <td>
                                <span class="status-badge status-<?php 
                                    echo $app['status'] === 'Новая' ? 'new' : 
                                        ($app['status'] === 'Идет обучение' ? 'progress' : 'completed');
                                ?>">
                                    <?php echo htmlspecialchars($app['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?></td>
                            <td>
                                <?php if (!empty($app['review'])): ?>
                                    <span title="<?php echo htmlspecialchars($app['review']); ?>">
                                        <i class="fas fa-comment"></i> Есть
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['status'] === 'Обучение завершено' && empty($app['review'])): ?>
                                    <button class="btn btn-sm btn-success" onclick="showReviewForm(<?php echo $app['id']; ?>)">
                                        <i class="fas fa-star"></i> Оставить отзыв
                                    </button>
                                    <div id="review-form-<?php echo $app['id']; ?>" style="display: none; margin-top: 10px;">
                                        <form action="review_handler.php" method="POST" class="d-flex gap-2">
                                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                            <input type="text" name="review" class="form-control" placeholder="Ваш отзыв..." required maxlength="500">
                                            <button type="submit" class="btn btn-sm btn-success">Отправить</button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="hideReviewForm(<?php echo $app['id']; ?>)">Отмена</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <script>
    function showReviewForm(id) {
        document.getElementById('review-form-' + id).style.display = 'flex';
    }
    function hideReviewForm(id) {
        document.getElementById('review-form-' + id).style.display = 'none';
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</html>