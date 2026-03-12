<?php
// Файл: admin/dashboard.php
session_start();
$page_title = 'Панель администратора - Корочки.есть';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../php/application_functions.php';

$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 5;

$applications = getAllApplicationsFiltered($status_filter, $search, $page, $items_per_page);
$total_applications = getTotalApplicationsCount($status_filter, $search);
$total_pages = ceil($total_applications / $items_per_page);

include '../includes/header.php';
?>

<!-- Скрытые элементы для передачи сообщений в JavaScript -->
<?php if (isset($_SESSION['admin_success'])): ?>
    <div data-success-message="<?php echo htmlspecialchars($_SESSION['admin_success']); ?>" style="display: none;"></div>
    <?php unset($_SESSION['admin_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div data-error-message="<?php echo htmlspecialchars($_SESSION['admin_error']); ?>" style="display: none;"></div>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<html>
    <head>
        <link rel="stylesheet" href="../css/style.css">
        <base href="http://localhost/3korochki/"> 
        <meta charset="UTF-8">
        <title><?php echo $page_title; ?></title>
    </head>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Панель администратора</h2>
                    
                    <!-- Фильтры -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label for="status-filter" class="form-label">Фильтр по статусу</label>
                            <select id="status-filter" class="form-select" onchange="filterByStatus(this.value)">
                                <option value="all" <?php echo $status_filter === '' ? 'selected' : ''; ?>>Все статусы</option>
                                <option value="Новая" <?php echo $status_filter === 'Новая' ? 'selected' : ''; ?>>Новые</option>
                                <option value="Идет обучение" <?php echo $status_filter === 'Идет обучение' ? 'selected' : ''; ?>>В процессе</option>
                                <option value="Обучение завершено" <?php echo $status_filter === 'Обучение завершено' ? 'selected' : ''; ?>>Завершенные</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search-input" class="form-label">Поиск</label>
                            <div class="input-group">
                                <input type="text" id="search-input" class="form-control" 
                                    placeholder="Поиск по ФИО или курсу" 
                                    value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-primary" onclick="searchApplications()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
                                <i class="fas fa-undo"></i> Сбросить фильтры
                            </button>
                        </div>
                    </div>
                    
                    <!-- Статистика -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3><?php echo $total_applications; ?></h3>
                                    <p class="mb-0">Всего заявок</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица заявок -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
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
                                <?php if (empty($applications)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Заявки не найдены</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td>#<?php echo $app['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($app['user_name']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($app['user_email']); ?></small>
                                            </td>
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
                                                <form action="update_status.php" method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                                        <option value="Новая" <?php echo $app['status'] === 'Новая' ? 'selected' : ''; ?>>Новая</option>
                                                        <option value="Идет обучение" <?php echo $app['status'] === 'Идет обучение' ? 'selected' : ''; ?>>В процессе</option>
                                                        <option value="Обучение завершено" <?php echo $app['status'] === 'Обучение завершено' ? 'selected' : ''; ?>>Завершена</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Пагинация -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="#" onclick="goToPage(<?php echo $page - 1; ?>)">Назад</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="#" onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="#" onclick="goToPage(<?php echo $page + 1; ?>)">Вперед</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/notifications.js"></script>

    <?php include '../includes/footer.php'; ?>
</html>