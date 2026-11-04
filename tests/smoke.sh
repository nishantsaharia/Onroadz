#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
find app admin api bin . -maxdepth 4 -name '*.php' -not -path './.git/*' -print0 | sort -zu | xargs -0 -n1 php -l >/dev/null
php -S 127.0.0.1:8877 router.php >/tmp/saafaar-http-smoke.log 2>&1 &
PID=$!
trap 'kill "$PID" 2>/dev/null || true' EXIT
sleep 2
for path in "/" "/cars/" "/travel-guides/" "/travel-guides/tawang-mountain-drive/" "/search/" "/request-booking/" "/booking-confirmation/" "/manage-booking/" "/enquire/" "/offline/" "/contact/" "/northeast-india/" "/guwahati-airport-car-rental/" "/admin/"; do
  status="$(curl --max-time 10 -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:8877$path")"
  [ "$status" = 200 ] || { echo "FAIL $path: $status"; cat /tmp/saafaar-http-smoke.log; exit 1; }
  echo "200 $path"
done
status="$(curl --max-time 10 -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8877/404/)"
[ "$status" = 404 ] || { echo "FAIL 404 status: $status"; exit 1; }
echo "PASS 15 smoke checks"
