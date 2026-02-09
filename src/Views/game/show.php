<h1>Game <?= htmlspecialchars($game->getId()) ?></h1>

<p>Status: <?= $game->getStatus()->value ?></p>
<p>Created by User: <?= $game->getCreatedByUserId() ?></p>

<h2>Rules</h2>
<pre><?php var_dump($game->getRules()->toArray()); ?></pre>

<h2>State</h2>
<pre><?php var_dump($game->getState()->toArray()); ?></pre>
