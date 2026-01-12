<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.4;">
    <h2>{{ $when }}</h2>

    <p>
      Yth. Pelanggan,<br/>
      Ini adalah pengingat tagihan layanan internet Anda.
    </p>

    <table cellpadding="6" cellspacing="0" border="0" style="border-collapse: collapse;">
      <tr>
        <td><strong>Invoice</strong></td>
        <td>{{ $invoice->invoice_number ?? ('#'.$invoice->id) }}</td>
      </tr>
      <tr>
        <td><strong>Periode</strong></td>
        <td>{{ $invoice->period_key ?? '-' }}</td>
      </tr>
      <tr>
        <td><strong>Tanggal Terbit</strong></td>
        <td>{{ $invoice->issue_date }}</td>
      </tr>
      <tr>
        <td><strong>Jatuh Tempo</strong></td>
        <td>{{ $invoice->due_date }}</td>
      </tr>
      <tr>
        <td><strong>Total</strong></td>
        <td>{{ $invoice->total }}</td>
      </tr>
      <tr>
        <td><strong>Status</strong></td>
        <td>{{ $invoice->status }}</td>
      </tr>
    </table>

    <p style="margin-top: 16px;">
      Mohon segera melakukan pembayaran sebelum melewati jatuh tempo untuk menghindari pembatasan layanan.
    </p>

    <p>
      Terima kasih,<br/>
      Maroon-NET
    </p>
  </body>
</html>