(function() {
  'use strict';
  var today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"][name="reservation_date"]').forEach(function(el){ el.min=today; });

  function encode(s) { return encodeURIComponent(s || ''); }

  document.querySelectorAll('form[data-static-form]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var fd = new FormData(form);
      var type = form.getAttribute('data-static-form');
      var name = fd.get('name') || '';
      var email = fd.get('email') || '';
      var phone = fd.get('phone') || '';
      var message = fd.get('message') || '';
      var subject = fd.get('subject') || 'Website enquiry';

      if (type === 'contact') {
        var body = 'Name: '+name+'\nEmail: '+email+'\nPhone: '+phone+'\n\nMessage:\n'+message;
        window.location.href = 'mailto:reserve@embersaffron.com?subject='+encode(subject)+'&body='+encode(body);
        form.querySelector('.form-status').textContent = 'Your email app should open now. If it does not, email reserve@embersaffron.com directly.';
      } else {
        var date = fd.get('reservation_date') || '';
        var time = fd.get('reservation_time') || '';
        var guests = fd.get('guests') || '';
        var occasion = fd.get('occasion') || '';
        var notes = fd.get('notes') || '';
        var ref = 'ES-'+Math.random().toString(36).substring(2,8).toUpperCase();
        var body = 'Reservation reference: '+ref+'\nName: '+name+'\nPhone: '+phone+'\nEmail: '+email+'\nDate: '+date+'\nTime: '+time+'\nGuests: '+guests+'\nOccasion: '+occasion+'\nNotes: '+notes;
        var wa = 'https://wa.me/8801711234567?text='+encode(body);
        var box = form.closest('.form-column').querySelector('.static-confirm');
        if (box) {
          box.hidden=false;
          box.innerHTML='<p class="confirm__label">Request prepared</p><h3>Reference: '+ref+'</h3><p>Your booking request is ready. Send it to us on WhatsApp so the team can confirm it.</p><p><a class="btn btn--primary" target="_blank" rel="noopener" href="'+wa+'">Send booking on WhatsApp</a> <a class="btn btn--outline" href="mailto:reserve@embersaffron.com?subject='+encode('Table reservation '+ref)+'&body='+encode(body)+'">Send by email</a></p>';
          box.scrollIntoView({behavior:'smooth', block:'start'});
        }
      }
    });
  });
})();