<h1 style="color:blue;">🎓 Binance Trading</h1> 
<hr/>
<br/>

<!--
<h3> Handle Klines Data: <button class="toggle-btn" data-type="course" >Open form</button> </h3>
<div class="course-form-wrapper create-course-wrapper">
    <form class="course-form" data-type="klines"  data-data_id=0 >
        <select id="aim" style="width: 48%;">
            <option value="">-- Select aim --</option>
            <option value="create">Create tables</option>
            <option value="insert">Insert klines</option>
            <option value="waitlist">Update klines</option>
        </select>
        <select id="interval" style="width: 48%;">
            <option value="">-- Select interval --</option>
            <option value="1d">Days</option>
            <option value="1h">Hours</option>
            <option value="1m">Minutes</option>
        </select>
        <button type="button" class="btn create-btn" data-goal="handle" data-type="klines" >Handle Klines</button>
    </form>
</div>
<hr/>
<br/>
-->

<h2 id="heading">Available Candidates (<?= count($candidates) ?>)</h2>
<div class="courses-grid">
    <?php if(!empty($candidates)): ?>
        <?php foreach($candidates as $symbol => $candidate): ?>
        <div class="data-card" data-type="signal" >
            <div class="course-card">
                <h3 class="card-title" title="Click to expand/shrink"><?= htmlspecialchars_decode($symbol) ?> 
                    <span class="side-caret"> 🔻 </span>
                </h3>
                <div class=" card-list">
                    <p>
                        <strong>Symbol:</strong> <?= htmlspecialchars_decode($symbol) ?>
                    </p>
                    <p>
                        <strong>FinalS Score:</strong> <?= htmlspecialchars_decode($candidate['finalScore']) ?>
                    </p>
                    <p>
                        <strong>Entry State:</strong> <?= htmlspecialchars_decode($candidate['state']) ?>
                    </p>
                    <p>
                        <strong>Current Price:</strong> <?= htmlspecialchars_decode($candidate['price']) ?>
                    </p>
                    <p>
                        <strong>Entry Price:</strong> <?= htmlspecialchars_decode($candidate['entry']) ?>
                    </p>
                    <p>
                        <strong>Stop Loss:</strong> <?= htmlspecialchars_decode($candidate['stopLoss']) ?>
                    </p>
                    <p>
                        <strong>Quantity:</strong> <?= htmlspecialchars_decode($candidate['qty']) ?>
                    </p>
                    <p>
                        <strong>Take Profits 1:</strong> <?= htmlspecialchars_decode($candidate['tp1Price']) ?>
                    </p>
                    <p>
                        <strong>Take Profits 2:</strong> <?= htmlspecialchars_decode($candidate['tp2Price']) ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No candidates retrieved yet.</p>
    <?php endif; ?>
</div>
