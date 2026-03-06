<?php
$page_title = 'Student Directory';
require_once 'templates/header.php';

$institute_id = get_institute_id();
$message = '';

// Fetch Classes for dropdown
$stmt = $pdo->prepare("SELECT * FROM classes WHERE institute_id = ? ORDER BY class_name ASC");
$stmt->execute([$institute_id]);
$classes = $stmt->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $class_id = $_POST['class_id'];
    $phone = $_POST['phone'];
    $parent_name = $_POST['parent_name'];
    $session = $_POST['session'];

    $stmt = $pdo->prepare("INSERT INTO students (institute_id, class_id, name, roll_no, phone, parent_name, session) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$institute_id, $class_id, $name, $roll_no, $phone, $parent_name, $session])) {
        $message = "Student registered successfully!";
    }
}

// Fetch Students
$stmt = $pdo->prepare("SELECT s.*, c.class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.institute_id = ? ORDER BY s.name ASC");
$stmt->execute([$institute_id]);
$students = $stmt->fetchAll();
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
    <h3 style="margin: 0;"><i class="fas fa-users" style="opacity: 0.5;"></i> <?php echo count($students); ?> Students</h3>
    <button onclick="document.getElementById('addStudentModal').style.display='flex'" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Register New
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php
endif; ?>

<!-- Desktop Table View -->
<div class="glass-card table-container student-table" style="padding: 0;">
    <table>
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Class / Course</th>
                <th>Contact</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($students):
    foreach ($students as $student): ?>
                <tr>
                    <td><span class="badge badge-red"><?php echo $student['roll_no'] ?: 'N/A'; ?></span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 35px; height: 35px; background: #f1f5f9; border-radius: 50%; display: grid; place-items: center; color: var(--secondary);">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <strong><?php echo $student['name']; ?></strong><br>
                                <small style="color: var(--secondary);">P: <?php echo $student['parent_name']; ?></small>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-weight: 600; color: var(--black);"><?php echo $student['class_name'] ?: 'No Class'; ?></span></td>
                    <td><i class="fas fa-phone-alt" style="font-size: 0.75rem; color: var(--secondary);"></i> <?php echo $student['phone']; ?></td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 5px; justify-content: flex-end;">
                            <a href="collect_fee.php?student_id=<?php echo $student['id']; ?>" class="btn btn-primary" style="padding: 5px 12px; font-size: 0.75rem;">Pay</a>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem;" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="student_ledger.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem;" title="Ledger"><i class="fas fa-file-lines"></i></a>
                            <a href="id_card.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem;" title="ID Card"><i class="fas fa-id-badge"></i></a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem; color: var(--danger);" title="Delete" data-confirm="Are you sure you want to delete this student and all their fee records?" data-title="Delete Student?"><i class="fas fa-trash-can"></i></a>
                        </div>
                    </td>
                </tr>
            <?php
    endforeach;
else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 3rem;">No students registered yet.</td></tr>
            <?php
endif; ?>
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="student-list-mobile fade-in">
    <?php if ($students):
    foreach ($students as $student): ?>
        <div class="student-card shadow-sm">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; gap: 12px;">
                    <div style="width: 45px; height: 45px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; display: grid; place-items: center; color: var(--primary);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1rem;"><?php echo $student['name']; ?></h4>
                        <div style="font-size: 0.75rem; color: var(--secondary); margin-top: 2px;">
                            <span>Roll: <?php echo $student['roll_no'] ?: '-'; ?></span> • 
                            <span>Class: <?php echo $student['class_name']; ?></span>
                        </div>
                    </div>
                </div>
                <a href="collect_fee.php?student_id=<?php echo $student['id']; ?>" style="color: var(--primary); font-weight: 800; text-decoration: none; font-size: 0.8rem;">PAY FEE</a>
            </div>
            <div class="actions">
                <a href="student_ledger.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.75rem;"><i class="fas fa-file-invoice"></i> Ledger</a>
                <a href="id_card.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.75rem;"><i class="fas fa-id-card"></i> ID Card</a>
                <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm" style="flex: 0.2; font-size: 0.75rem; color: var(--danger);" data-confirm="Delete this student?" data-title="Delete Student?"><i class="fas fa-trash-can"></i></a>
            </div>
        </div>
    <?php
    endforeach;
endif; ?>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="modal-overlay">
    <div class="glass-card modal-content fade-in" style="padding: 2rem; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>New Registration</h3>
            <button onclick="document.getElementById('addStudentModal').style.display='none'" style="border:none; background:none; font-size: 1.5rem; cursor:pointer; color: var(--secondary);">&times;</button>
        </div>
        <form action="students.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Student's Legal Name" required>
            </div>
            <div class="grid-2" style="gap: 1rem;">
                <div class="form-group">
                    <label>Roll Number</label>
                    <input type="text" name="roll_no" class="form-control" placeholder="ID or Roll">
                </div>
                <div class="form-group">
                    <label>Class/Course</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">-- Choose --</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo $class['class_name']; ?></option>
                        <?php
endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="Primary Contact">
            </div>
            <div class="form-group">
                <label>Parent/Guardian</label>
                <input type="text" name="parent_name" class="form-control" placeholder="Name of Father/Mother">
            </div>
            <div class="form-group">
                <label>Academic Session</label>
                <input type="text" name="session" class="form-control" placeholder="e.g. 2024-2026">
            </div>
            <button type="submit" name="add_student" class="btn btn-primary btn-block" style="margin-top: 1rem;"><i class="fas fa-save"></i> Register Student</button>
        </form>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
