<article>
    <h2>Stored XSS — When browsers trust user input</h2>
    <p>Stored XSS happens when user input is saved and later rendered without escaping. The browser sees HTML or scripts and executes them.</p>

    <h3>Vulnerable rendering</h3>
    <pre><code>// VULNERABLE
echo $_POST['comment'];
</code></pre>

    <h3>Why it matters</h3>
    <p>An attacker can inject scripts to steal sessions, deface pages, or alter content for every visitor who loads the page.</p>

    <h3>Secure fix</h3>
    <pre><code>// SECURE
echo htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
</code></pre>

    <h3>Best practices</h3>
    <ul>
        <li>Escape all dynamic output in HTML.</li>
        <li>Consider a Content Security Policy for defense-in-depth.</li>
        <li>Validate and sanitize user inputs before storage.</li>
    </ul>
</article>
