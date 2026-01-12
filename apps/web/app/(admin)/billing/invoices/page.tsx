// inside MarkPaidButton fetch body
body: JSON.stringify({
  paid_at: new Date().toISOString(),
  payment_method: "manual",
}),