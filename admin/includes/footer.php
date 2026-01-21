    </main>
  </div>
</div>
<script>
  // mark active
  (function(){
    const path = location.pathname.split('/').pop();
    document.querySelectorAll('.nav-link').forEach(a=>{
      const href = a.getAttribute('href');
      if (href === path) a.classList.add('active');
    });
  })();
</script>
</body>
</html>
