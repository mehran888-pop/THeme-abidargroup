# بکاپ‌های قالب آبیدار

هر فایل `.tar.gz` یک نسخه کامل از کد قالب در لحظه ساخت است (بدون `.git`، `node_modules` و فایل‌های zip).

## بازگردانی

```bash
# ۱. محتوای بکاپ را در یک پوشه موقت باز کنید
mkdir /tmp/restore && tar -xzf backups/abidar-theme-backup-XXXX.tar.gz -C /tmp/restore

# ۲. فایل‌های فعلی را با بکاپ جایگزین کنید
rsync -a --delete /tmp/restore/ /path/to/wp-content/themes/abidar-group-holding/
```

## نسخه‌ها

| فایل | نسخه قالب | کامیت | تاریخ |
|---|---|---|---|
| `abidar-theme-backup-20261110-131352-e0f692d.tar.gz` | 2.4.1 | `e0f692d` | پیش از افزودن هیرو، لندینگ و گزارش امنیتی |
