<?php
// Attogram Framework - Page Footer v0.2.0

namespace Attogram;

$divider = '&nbsp;&nbsp; | &nbsp;&nbsp;';
echo '
<footer class="footer">
 <div class="container-fluid">
  <p>
    <small>
    <span style="white-space: nowrap"><a href="'.$this->get_site_url().'/">'.$this->site_name.'</a></span>
    '.$divider.'
    <span style="white-space: nowrap">🚀 Powered by <a target="github" href="'.$this->project_github.'">Attogram <small>v'.attogram::ATTOGRAM_VERSION.'</small></a></span>
    '.$divider.'
    <span style="white-space: nowrap">🕑 '.gmdate('Y-m-d H:i:s').' UTC</span>
    '.$divider.'
    <span style="white-space: nowrap">👤 '.$this->clientIp.'</span>
    '.$divider.'
    <span style="white-space: nowrap">🏁 '.round((microtime(1) - $this->start_time), 3, PHP_ROUND_HALF_UP).' seconds</span>
    </small>
  </p>
 </div>
</footer>
</body></html>';
