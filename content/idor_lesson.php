<article>
    <h2>IDOR — Insecure Direct Object Reference</h2>
    <p>IDOR happens when internal object IDs are exposed and the server fails to enforce authorization. Attackers can guess IDs and access data that is not theirs.</p>

    <h3>Vulnerable pattern</h3>
    <pre><code>// VULNERABLE
$fileId = $_GET['file_id'];
$file = getFileById($fileId);
</code></pre>

    <h3>Secure fix</h3>
    <pre><code>// SECURE
$fileId = $_GET['file_id'];
$file = getFileByIdAndUser($fileId, $currentUserId);
</code></pre>

    <h3>Best practices</h3>
    <ul>
        <li>Always check object ownership on the server.</li>
        <li>Prefer non-sequential IDs or UUIDs.</li>
        <li>Log and monitor access attempts.</li>
    </ul>
</article>
