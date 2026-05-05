<article>
    <h2>SQL Injection — Break the query</h2>
    <p>Imagine a shopping list that anyone can rewrite as you read it. SQL Injection happens when user input is glued directly into a database query, letting attackers rewrite the query itself.</p>

    <h3>How the vulnerable code looks</h3>
    <pre><code>// VULNERABLE — Never do this
$username = $_POST['username'];
$password = $_POST['password'];
$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $pdo->query($query);
</code></pre>

    <h3>What the payload does</h3>
    <p>If the attacker enters <code>' OR '1'='1</code>, the query becomes true for every row, bypassing authentication.</p>

    <h3>Secure fix with PDO</h3>
    <pre><code>// SECURE — Prepared statement
$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE username = :username AND password_hash = :password"
);
$stmt->execute([
    ':username' => $_POST['username'],
    ':password' => $_POST['password']
]);
</code></pre>

    <h3>Best practices</h3>
    <ul>
        <li>Always use prepared statements with bound parameters.</li>
        <li>Validate input and keep DB permissions minimal.</li>
        <li>Never concatenate user input into SQL strings.</li>
    </ul>
</article>
