<?php
// Enable error reporting (for debugging; disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// InfinityFree database connection
$host = "sql103.infinityfree.com";
$username = "if0_40271114";
$password = "QdO20m5hR4JbOHe";
$dbname = "if0_40271114_peer_review_db";

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get PRID from URL
$pr_id = $_GET['pr_id'] ?? null;

// Fetch data
if ($pr_id) {
    $stmt = $mysqli->prepare("SELECT * FROM pr_submissions WHERE pr_id = ?");
    $stmt->bind_param("s", $pr_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();
    $stmt->close();

    $answers = !empty($feedback['answers']) ? json_decode($feedback['answers'], true) : [];
    $questions_result = $mysqli->query("SELECT * FROM questions");
} else {
    $conditions = [];
    $params = [];
    $types = '';

    // Status filter
    if (!empty($_GET['status'])) {
        $conditions[] = "status = ?";
        $params[] = $_GET['status'];
        $types .= 's';
    }

    // Builder name filter (partial match)
    if (!empty($_GET['builder'])) {
        $conditions[] = "builder_name LIKE ?";
        $params[] = '%' . $_GET['builder'] . '%';
        $types .= 's';
    }

    // Submission date filter
    if (!empty($_GET['submission_date'])) {
        $conditions[] = "DATE(submission_date) = ?";
        $params[] = $_GET['submission_date'];
        $types .= 's';
    }

    $sql = "SELECT * FROM pr_submissions";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $sql .= " ORDER BY submission_date DESC";

    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PR Feedback</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="pr_feedback.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
<header>
    <h1>Peer Review Feedback</h1>
    <p>Review the submitted feedback and responses.</p>
</header>

<?php if (!$pr_id): ?>
<!-- Filter and Search Buttons -->
<button id="toggleFilterBtn" type="button" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-filter"></i> Filters
</button>
<button id="toggleSearchBtn" type="button" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-search"></i> Search PRID
</button>

<!-- Filter Form: Hidden by default -->
<form method="GET" action="pr_feedback.php" class="mb-3 row g-2 align-items-end" id="filterForm" style="display:none;">
    <div class="col-md-3">
        <label for="statusFilter" class="form-label">Status</label>
        <select name="status" id="statusFilter" class="form-select">
            <option value="">All</option>
            <option value="Pending - Builder Notified" <?= ($_GET['status'] ?? '') === 'Pending - Builder Notified' ? 'selected' : '' ?>>Pending - Builder Notified</option>
            <option value="Completed - Valid" <?= ($_GET['status'] ?? '') === 'Completed - Valid' ? 'selected' : '' ?>>Completed - Valid</option>
            <option value="Completed - Invalid" <?= ($_GET['status'] ?? '') === 'Completed - Invalid' ? 'selected' : '' ?>>Completed - Invalid</option>
            <option value="Other" <?= ($_GET['status'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="builderFilter" class="form-label">Builder Name</label>
        <input type="text" name="builder" id="builderFilter" class="form-control" value="<?= htmlspecialchars($_GET['builder'] ?? '') ?>" placeholder="Builder Name">
    </div>
    <div class="col-md-3">
        <label for="dateFilter" class="form-label">Submission Date</label>
        <input type="date" name="submission_date" id="dateFilter" class="form-control" value="<?= htmlspecialchars($_GET['submission_date'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100" type="submit">Filter</button>
        <?php if (!empty($_GET['status']) || !empty($_GET['builder']) || !empty($_GET['submission_date'])): ?>
            <a href="pr_feedback.php" class="btn btn-secondary w-100 mt-2">Clear</a>
        <?php endif; ?>
    </div>
</form>

<!-- Search PRID Input: Hidden by default -->
<form method="GET" action="pr_feedback.php" id="searchPridForm" style="display:none;" class="mb-3">
    <div class="d-flex align-items-center gap-2">
        <label for="searchPridInput" class="form-label mb-0" style="font-size:1.1rem; cursor: default;">🔍 PRID :</label>
        <input type="text" name="pr_id" id="searchPridInput" class="form-control" placeholder="Enter PRID" style="max-width: 250px;">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<script>
    const filterBtn = document.getElementById('toggleFilterBtn');
    const searchBtn = document.getElementById('toggleSearchBtn');
    const filterForm = document.getElementById('filterForm');
    const searchForm = document.getElementById('searchPridForm');

    filterBtn.addEventListener('click', function() {
        if (filterForm.style.display === 'none' || filterForm.style.display === '') {
            filterForm.style.display = 'flex';   // Show filter form
            searchForm.style.display = 'none';   // Hide search form
        } else {
            filterForm.style.display = 'none';
        }
    });

    searchBtn.addEventListener('click', function() {
        if (searchForm.style.display === 'none' || searchForm.style.display === '') {
            searchForm.style.display = 'block';  // Show search form
            filterForm.style.display = 'none';   // Hide filter form
        } else {
            searchForm.style.display = 'none';
        }
    });
</script>

<?php endif; ?>

<?php if ($pr_id): ?>
<a href="pr_feedback.php" class="btn btn-secondary mb-4">Back to All Feedback</a>

<?php if ($feedback): ?>
<div class="feedback-card">
    <div class="taskInfo">
        <h3><strong><?= htmlspecialchars($feedback['task_name']) ?></strong></h3>
        <p><strong><?= htmlspecialchars($feedback['pr_id']) ?></strong></p>
        <p><strong>Status:</strong> 
        <?php
        $status = $feedback['status'];
        $status_colors = [
            "Completed - Valid" => ['icon' => 'bi bi-check-circle-fill', 'bg' => '#28a745', 'color' => '#fff'],
            "Completed - Invalid" => ['icon' => 'bi bi-x-circle-fill', 'bg' => '#dc3545', 'color' => '#fff'],
            "Pending - Builder Notified" => ['icon' => 'bi bi-arrow-repeat bi-spin', 'bg' => '#ffc107', 'color' => '#212529'],
            "Other" => ['icon' => 'bi bi-hourglass-split', 'bg' => '#ffc107', 'color' => '#212529'],
        ];
        $icon = $status_colors[$status]['icon'] ?? 'bi bi-hourglass-split';
        $bg = $status_colors[$status]['bg'] ?? '#ffc107';
        $color = $status_colors[$status]['color'] ?? '#212529';
        echo "<span id='prStatus' style='display:inline-block;padding:5px 12px;border-radius:12px;color:$color;font-weight:bold;font-size:0.9rem;background-color:$bg;'><i class='$icon' style='margin-right:5px;'></i>" . htmlspecialchars($status) . "</span>";
        ?>
        </p>
        <p><strong>Peer Reviewer:</strong> <?= htmlspecialchars(ucwords($feedback['peer_reviewer_name'])) ?> (<?= htmlspecialchars($feedback['peer_reviewer_email']) ?>)</p>
        <p><strong>Builder:</strong> <?= htmlspecialchars(ucwords($feedback['builder_name'])) ?> (<?= htmlspecialchars($feedback['builder_email']) ?>)</p>
        <p><strong>Date:</strong> <?= htmlspecialchars($feedback['submission_date']) ?></p>
    </div>

    <h4>Feedback</h4>
    <ul>
    <?php 
    while ($question = $questions_result->fetch_assoc()) {
        $qid = $question['question_id'];
        $answer = $answers['q'.$qid] ?? null;
        if ($answer && strtolower($answer) !== 'not applicable') {
            echo "<li><strong>" . htmlspecialchars($question['question_text']) . "</strong><br>";
            echo "<strong>Answer:</strong> " . htmlspecialchars($answer);

            if (strtolower($answer) === "applicable") {
                $fatality = $answers['fatality'.$qid] ?? null;
                $fatality_display = $fatality === 'fatal' ? "<span class='highlight'>Fatal Error</span>" : ($fatality === 'nonFatal' ? "Non-Fatal Error" : "Not specified");
                echo "<br><strong>Fatality:</strong> $fatality_display";

                $remarks = $answers['remarks'.$qid] ?? 'No remarks provided';
                echo "<br><strong>Remarks:</strong> " . htmlspecialchars($remarks);
            }

            // Proof images
            $images = isset($feedback['image_paths']) ? json_decode($feedback['image_paths'], true) : [];
            if (!empty($images['q'.$qid])) {
                echo "<br><strong>Proof:</strong><br>";
                foreach ($images['q'.$qid] as $img) {
                    $path = '../uploads/' . htmlspecialchars($img);
                    echo "<img src='$path' class='img-thumbnail preview-image' alt='Proof Image' style='max-width:150px;margin:5px;cursor:pointer;' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='$path' />";
                }
            } else {
                echo "<p>No images uploaded.</p>";
            }
            echo "</li><hr>";
        }
    }
    ?>
    </ul>

    <div class="mb-3">
        <button type="button" class="btn btn-primary" id="sendEmailAjax" data-prid="<?= htmlspecialchars($pr_id) ?>"><i class="bi bi-send"></i> Send Email</button>
        <button type="button" class="btn btn-success" id="markValid">Mark as Valid</button>
        <button type="button" class="btn btn-danger" id="markInvalid">Mark as Invalid</button>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">No feedback found for PRID <?= htmlspecialchars($pr_id) ?>.</div>
<?php endif; ?>

<?php else: ?>
<div class="feedback-table">
    <h3>All PR Feedbacks</h3>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>PRID</th>
                    <th>Task Name</th>
                    <th>Status</th>
                    <th>Submission Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($feedback = $result->fetch_assoc()): ?>
                <?php $shortTask = mb_strimwidth($feedback['task_name'], 0, 50, '...'); ?>
                <tr>
                    <td><?= htmlspecialchars($feedback['pr_id']) ?></td>
                    <td title="<?= htmlspecialchars($feedback['task_name']) ?>"><?= htmlspecialchars($shortTask) ?></td>
                    <td><?= htmlspecialchars($feedback['status']) ?></td>
                    <td><?= htmlspecialchars($feedback['submission_date']) ?></td>
                    <td><a href="pr_feedback.php?pr_id=<?= htmlspecialchars($feedback['pr_id']) ?>" class="btn btn-info btn-sm">View Feedback</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body text-center">
        <img id="modalImage" src="" class="img-fluid rounded" alt="Preview Image">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.preview-image').forEach(img => {
  img.addEventListener('click', () => {
    document.getElementById('modalImage').src = img.dataset.imgSrc;
  });
});

document.addEventListener('DOMContentLoaded', function() {
  const prId = new URLSearchParams(window.location.search).get("pr_id");
  const statusElement = document.getElementById('prStatus');

  async function updateStatus(newStatus) {
    const res = await fetch('update_status.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: new URLSearchParams({pr_id: prId, status: newStatus})
    });
    const data = await res.json();
    if(data.success){
      const colors = {
        'Completed - Valid': ['bi bi-check-circle-fill','#28a745','#fff'],
        'Completed - Invalid':['bi bi-x-circle-fill','#dc3545','#fff'],
        'Pending - Builder Notified':['bi bi-arrow-repeat bi-spin','#ffc107','#212529'],
        'Other':['bi bi-hourglass-split','#ffc107','#212529']
      };
      let [icon,bg,color] = colors[newStatus]||colors['Other'];
      statusElement.innerHTML=`<i class="${icon}" style="margin-right:5px;"></i>${newStatus}`;
      statusElement.style=`display:inline-block;padding:5px 12px;border-radius:12px;font-weight:bold;font-size:0.9rem;color:${color};background-color:${bg};`;
      Swal.fire({icon:'success',title:'Status Updated',text:`Status changed to "${newStatus}"`,timer:1500,showConfirmButton:false});
    } else {
      Swal.fire('Error', data.message||'Status update failed.','error');
    }
  }

  document.getElementById('markValid')?.addEventListener('click',()=>updateStatus('Completed - Valid'));
  document.getElementById('markInvalid')?.addEventListener('click',()=>updateStatus('Completed - Invalid'));

  document.getElementById('sendEmailAjax')?.addEventListener('click', async () => {
    Swal.fire({title:"Sending Email...",text:"Please wait...",allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
    try{
      const res = await fetch('send_email.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:new URLSearchParams({pr_id:prId,ajax:1})
      });
      let data;
      try { data=await res.json(); } catch { data={success:false,message:await res.text()}; }
      Swal.close();
      if(data.success){ Swal.fire("✅ Success",data.message,"success"); await updateStatus("Pending - Builder Notified"); }
      else Swal.fire("❌ Failed",data.message||"Email sending failed.","error");
    } catch(err){ Swal.close(); Swal.fire("Error","Request failed:<br><pre>"+err+"</pre>","error"); }
  });
});
</script>
</body>
</html>
