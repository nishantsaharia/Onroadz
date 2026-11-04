#!/usr/bin/env python3
"""Repackage the existing SAAFAAR PHP site with approved live SAAFAAR images
and a unified responsive Aether visual upgrade. No database or payment gates
are changed. Run from the repository root with Python 3.
"""
from __future__ import annotations
import hashlib
import io
import pathlib
import re
import shutil
import tempfile
import urllib.request
import zipfile

REPO = pathlib.Path(__file__).resolve().parents[1]
SOURCE = REPO / "SAAFAAR-FINAL-WEBSITE.zip"
TARGET = REPO / "SAAFAAR-AETHER-PREMIUM-SEO-FINAL.zip"
PREFIX = "SAAFAAR-FINAL-WEBSITE"

IMAGES = {
    "meghalaya-waterfall-1600.webp": "https://saafaar.in/assets/img/meghalaya-waterfall-1600.webp",
    "meghalaya-hills-waterfalls-1600.webp": "https://saafaar.in/assets/img/meghalaya-hills-waterfalls-1600.webp",
    "arunachal-mountain-gateway-1024.webp": "https://saafaar.in/assets/img/arunachal-mountain-gateway-1024.webp",
    "kaziranga-rhino-1600.webp": "https://saafaar.in/assets/img/kaziranga-rhino-1600.webp",
    "saafaar-logo-optimized.webp": "https://saafaar.in/assets/logo/saafaar-logo-optimized.webp",
    "saafaar-logo-white.webp": "https://saafaar.in/assets/logo/saafaar-logo-white.webp",
    "saafaar-social.jpg": "https://saafaar.in/assets/og/saafaar-home.jpg",
}

def must_replace(text: str, old: str, new: str, label: str) -> str:
    if old not in text:
        raise RuntimeError("Expected patch anchor missing: " + label)
    return text.replace(old, new, 1)

def write_text(root: pathlib.Path, name: str, text: str) -> None:
    path = root / name
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(text, encoding="utf-8")

def build() -> None:
    if not SOURCE.is_file():
        raise FileNotFoundError(f"Missing base package: {SOURCE}")
    with tempfile.TemporaryDirectory(prefix="saafaar-aether-") as temp:
        root = pathlib.Path(temp) / PREFIX
        with zipfile.ZipFile(SOURCE) as archive:
            if archive.testzip():
                raise RuntimeError("Base ZIP CRC check failed")
            for item in archive.infolist():
                # Reject traversal paths from archives.
                rel = pathlib.PurePosixPath(item.filename)
                if rel.is_absolute() or ".." in rel.parts or not rel.parts or rel.parts[0] != PREFIX:
                    raise ValueError("Unsafe ZIP entry: " + item.filename)
                dest = pathlib.Path(temp).joinpath(*rel.parts)
                if item.is_dir():
                    dest.mkdir(parents=True, exist_ok=True)
                else:
                    dest.parent.mkdir(parents=True, exist_ok=True)
                    dest.write_bytes(archive.read(item))

        images_dir = root / "public/assets/media"
        images_dir.mkdir(parents=True, exist_ok=True)
        for name, url in IMAGES.items():
            req = urllib.request.Request(url, headers={"User-Agent": "SAAFAAR-source-repackager/1.0"})
            with urllib.request.urlopen(req, timeout=25) as res:
                payload = res.read(4_000_001)
                content_type = (res.headers.get("Content-Type") or "").lower()
            if len(payload) > 4_000_000:
                raise RuntimeError("Image too large: " + name)
            if name.endswith(".webp") and not (payload[:4] == b"RIFF" and payload[8:12] == b"WEBP"):
                raise RuntimeError("Invalid WebP: " + name + ", type " + content_type)
            if name.endswith(".jpg") and not payload.startswith(b"\xff\xd8"):
                raise RuntimeError("Invalid JPEG: " + name)
            (images_dir / name).write_bytes(payload)
            print("Bundled:", name, len(payload), "bytes")

        layout_path = root / "app/layout.php"
        layout = layout_path.read_text(encoding="utf-8")
        header_old = '<a class="wordmark" href="/" aria-label="SAAFAAR HOLIDAYS home">SAAFAAR<span>HOLIDAYS</span></a>'
        header_new = '<a class="wordmark" href="/" aria-label="SAAFAAR HOLIDAYS home"><img src="/assets/media/saafaar-logo-optimized.webp" width="160" height="56" alt="SAAFAAR HOLIDAYS"></a>'
        layout = must_replace(layout, header_old, header_new, "header logo")
        footer_old = '<a class="wordmark inverse" href="/">SAAFAAR<span>HOLIDAYS</span></a>'
        footer_new = '<a class="wordmark inverse" href="/"><img src="/assets/media/saafaar-logo-white.webp" width="160" height="56" alt="SAAFAAR HOLIDAYS"></a>'
        layout = must_replace(layout, footer_old, footer_new, "footer logo")
        layout = must_replace(layout,
            '<meta property="og:image" content="<?=e($cfg[\'base_url\'])?>/assets/media/social.png">',
            '<meta property="og:image" content="<?=e($cfg[\'base_url\'])?>/assets/media/saafaar-social.jpg"><meta name="twitter:image" content="<?=e($cfg[\'base_url\'])?>/assets/media/saafaar-social.jpg"><meta property="og:locale" content="en_IN">',
            "social image metadata")
        layout = must_replace(layout,
            '<html lang="en">',
            '<html lang="en-IN">',
            "document language")
        layout = must_replace(layout,
            '<link rel="stylesheet" href="/assets/css/style.css">',
            '<link rel="stylesheet" href="/assets/css/style.css"><?php if($path===\'/\'):?><link rel="preload" as="image" href="/assets/media/meghalaya-waterfall-1600.webp" type="image/webp"><?php endif;?>',
            "homepage image preload")
        # The destination component is the last function in layout.php.
        position = layout.index("function destination_cards():void")
        layout = layout[:position] + r'''function destination_cards():void {
$cards=[
 ['meghalaya-self-drive-car-rental','Meghalaya','Clouds, canyons & slower mornings','01','mist','meghalaya-hills-waterfalls-1600.webp','Forested Meghalaya hills and a waterfall'],
 ['arunachal-pradesh-road-trip','Arunachal Pradesh','Mountain roads. A wider perspective.','02','peak','arunachal-mountain-gateway-1024.webp','Mountain gateway in Arunachal Pradesh'],
 ['hornbill-festival-car-rental-guwahati','Nagaland','A journey into living culture','03','ember','','']
];
foreach($cards as [$slug,$name,$desc,$n,$style,$photo,$alt]):?>
<a class="destination-card <?=$style?>" href="/<?=e($slug)?>/">
<?php if($photo):?><img src="/assets/media/<?=e($photo)?>" width="1024" height="680" loading="lazy" decoding="async" alt="<?=e($alt)?>"><?php endif;?>
<span class="destination-number"><?=$n?> / EXPLORE</span>
<div class="contours" aria-hidden="true"></div>
<div><p class="eyebrow">NORTHEAST INDIA</p><h3><?=e($name)?></h3><p><?=e($desc)?></p></div>
<span class="round-arrow">↗</span>
<?php if(!$photo):?><small class="asset-note">Original Hornbill Festival photograph awaiting approval</small><?php endif;?>
</a>
<?php endforeach;
}
'''
        layout_path.write_text(layout, encoding="utf-8")

        front_path = root / "app/front-controller.php"
        front = front_path.read_text(encoding="utf-8")
        front = must_replace(front,
            '<h1>Go somewhere<br>that stays<br><em>with you.</em></h1>',
            '<h1>Self Drive Car Rental<br>in Guwahati.<br><em>Explore the Northeast.</em></h1>',
            "primary H1")
        front = must_replace(front,
            '<img src="/assets/media/route-art.svg" width="620" height="500" fetchpriority="high" alt="Abstract illustration of a winding road through mountain contours"><span class="art-caption">ARTWORK PLACEHOLDER · ORIGINAL PHOTOGRAPH PENDING</span>',
            '<img src="/assets/media/meghalaya-waterfall-1600.webp" width="1024" height="680" fetchpriority="high" loading="eager" decoding="async" alt="Original waterfall photograph from Meghalaya"><span class="art-caption">NORTHEAST INDIA · ORIGINAL TRAVEL PHOTOGRAPHY</span>',
            "homepage hero photograph")
        front = must_replace(front,
            '<div class="editorial-visual"><div class="contours" aria-hidden="true"></div><span class="big-number">NE.</span><small>YOUR ORIGINAL DESTINATION PHOTOGRAPH GOES HERE</small></div>',
            '<div class="editorial-visual"><img src="/assets/media/arunachal-mountain-gateway-1024.webp" width="1024" height="680" loading="lazy" decoding="async" alt="Scenic mountain route in Arunachal Pradesh"><span class="big-number">NE.</span><small>THE ROAD IS THE STORY</small></div>',
            "editorial photo")
        front = must_replace(front,
            '<section class="page-hero"><div class="wrap"><?php breadcrumb($p[\'title\']);?>',
            '<section class="page-hero <?=$p[\'kind\']===\'guide\'?\'travel-photo-hero destination-\'.e($slug):\'\'?>"><div class="wrap"><?php breadcrumb($p[\'title\']);?>',
            "destination guide hero style")
        front_path.write_text(front, encoding="utf-8")

        css_path = root / "public/assets/css/style.css"
        css = css_path.read_text(encoding="utf-8")
        css += r'''

/* SAAFAAR Aether premium visual upgrade, 2026-11-04.
   Keeps booking/security/admin behaviour unchanged. */
:root{--saafaar-red:#DC2626;--saafaar-darkred:#B91C1C;--saafaar-ink:#0c0d12;--saafaar-soft:#f8fafc}
body{font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;letter-spacing:-.005em}
h1,h2,h3{font-weight:750;letter-spacing:-.045em}
.header.wrap{max-width:1160px;margin:16px auto 18px;height:82px;padding:0 26px;position:relative;z-index:40;background:rgba(255,255,255,.96);border:1px solid rgba(15,23,42,.08);border-radius:32px;box-shadow:0 15px 42px rgba(17,24,39,.09);-webkit-backdrop-filter:blur(16px);backdrop-filter:blur(16px)}
.wordmark{line-height:0;min-width:146px}
.wordmark img{width:152px;height:auto;max-height:56px;object-fit:contain}
.wordmark.inverse img{width:155px;max-height:58px}
.header nav{gap:19px;font-size:12px}
.header-action{border-color:#e3e5e9;background:linear-gradient(120deg,#fff,#f8fafc);padding:12px 17px;font-weight:720}
.header-action:hover{border-color:#c92b34;color:#b91c1c}
.topline{background:#0b0c10}
.hero{color:white;background:radial-gradient(ellipse at 78% 25%,#31333e 0%,#13151b 41%,#090a0d 89%)}
.hero-grid{min-height:610px;grid-template-columns:1.06fr .94fr;gap:52px;padding-top:70px;padding-bottom:135px}
.hero h1{font-size:clamp(39px,4.75vw,73px);line-height:1.08;color:#fff;font-weight:830;letter-spacing:-.06em}
.hero h1 em{font-family:inherit;font-weight:830;color:#ef4444;font-style:normal}
.hero .eyebrow{color:#fb9c9d}
.hero .intro{color:#b7bac5;font-size:15px;line-height:1.85}
.hero .text-link{border-color:#7a7b85;color:#eef0f4}
.hero-art{height:460px;isolation:isolate;border-radius:27px;overflow:hidden;background:#1f2732;box-shadow:0 25px 70px rgba(0,0,0,.27)}
.hero-art img{height:100%;width:100%;object-fit:cover}
.hero-art:after{content:"";position:absolute;inset:0;z-index:0;background:linear-gradient(180deg,transparent 45%,rgba(3,5,9,.42) 100%);pointer-events:none}
.hero-art .art-caption{z-index:1;right:22px;bottom:22px;color:#fff;font-weight:700;font-size:10px;text-shadow:0 1px 5px #1118}
.hero-coordinates{color:#b9bbc2}
.hero-tag{color:#cdd0d9}
.search-wrap{margin-top:-77px}
.search-panel{border-radius:22px;background:rgba(255,255,255,.99);box-shadow:0 25px 70px rgba(17,24,39,.15);border:1px solid #fff;padding:29px 32px}
.search-title{font-weight:800;color:#111827}
.search-fields input,.search-fields select{border-color:#e2e6eb;background:#fafbfc}
.search-fields input:focus,.search-fields select:focus{background:#fff;border-color:#d62431}
.button{font-weight:750;border-radius:9px;box-shadow:0 8px 18px rgba(185,28,28,.1)}
.button:hover{box-shadow:0 14px 27px rgba(185,28,28,.18)}
.section{padding-top:95px;padding-bottom:95px}
.panel,.vehicle-card,.journal-card{border-radius:19px;box-shadow:0 10px 25px rgba(10,12,20,.035)}
.vehicle-card{transition:transform .25s ease,box-shadow .25s ease}
.vehicle-card:hover{transform:translateY(-4px);box-shadow:0 22px 50px rgba(12,14,23,.11)}
.destination-card{border-radius:21px;min-height:415px;background:linear-gradient(155deg,#576479,#171b24);isolation:isolate;box-shadow:0 18px 46px rgba(10,18,20,.1)}
.destination-card img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:-2;transition:transform .65s ease}
.destination-card:after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(2,6,15,.2) 0%,rgba(2,6,15,.18) 35%,rgba(2,6,15,.87) 100%)}
.destination-card:hover img{transform:scale(1.065)}
.destination-card .contours{z-index:-1;opacity:.07}
.destination-card.ember .contours{opacity:.3}
.destination-card .asset-note{color:#f5eded}
.editorial-visual{background:#222b32;border-radius:22px;isolation:isolate}
.editorial-visual img{position:absolute;inset:0;height:100%;width:100%;object-fit:cover;z-index:-2}
.editorial-visual:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 10%,rgba(7,11,16,.62));z-index:-1}
.editorial-visual .big-number{color:rgba(255,255,255,.77);font-size:140px}
.editorial-visual small{z-index:2;color:#fff;text-shadow:0 1px 5px #0009;letter-spacing:.16em;font-weight:800}
.page-hero{background:linear-gradient(135deg,#f8fafc,#f4f3f2);position:relative}
.page-hero.travel-photo-hero{background-size:cover;background-position:center;color:#fff;isolation:isolate}
.page-hero.travel-photo-hero:before{content:"";position:absolute;inset:0;background:linear-gradient(100deg,rgba(8,12,17,.87),rgba(8,12,17,.47));z-index:-1}
.page-hero.travel-photo-hero .lead{color:#d7dbe3}
.page-hero.travel-photo-hero .breadcrumb{color:#f5f6f8}
.destination-meghalaya-self-drive-car-rental,.destination-guwahati-to-shillong-road-trip{background-image:url('/assets/media/meghalaya-hills-waterfalls-1600.webp')}
.destination-arunachal-pradesh-road-trip,.destination-tawang-self-drive-car-rental,.destination-guwahati-to-tawang-road-trip,.destination-sela-pass-travel-guide{background-image:url('/assets/media/arunachal-mountain-gateway-1024.webp')}
.destination-northeast-india{background-image:url('/assets/media/kaziranga-rhino-1600.webp')}
.destination-hornbill-festival-car-rental-guwahati,.destination-mago-arunachal-pradesh-guide,.destination-bumla-pass-travel-guide{background-image:linear-gradient(130deg,#303846,#131821)}
.contact-band{background:linear-gradient(130deg,#B91C1C,#DC2626)}
@media(max-width:1180px){.header.wrap{margin:10px 20px;padding:0 20px;max-width:none}.hero-grid{gap:30px}}
@media(max-width:800px){.header.wrap{height:auto;min-height:72px;border-radius:24px}.hero-grid{grid-template-columns:1fr;padding-top:60px;padding-bottom:85px;gap:30px}.hero-art{height:320px}.hero h1{font-size:clamp(39px,8vw,65px)}.search-wrap{margin-top:-35px}.search-panel{padding:23px}}
@media(max-width:600px){.header.wrap{margin:10px 12px;padding:12px 16px;min-height:70px;border-radius:21px}.wordmark{min-width:125px}.wordmark img{width:130px;max-height:48px}.hero-grid{padding-top:55px;padding-bottom:66px}.hero-art{height:252px;border-radius:19px}.hero h1{font-size:clamp(35px,9.3vw,53px)}.hero .intro{font-size:13px}.search-wrap{margin-top:-22px}.search-panel{padding:21px 16px;border-radius:17px}.destination-card{min-height:330px}.editorial-visual{min-height:300px}.section{padding-top:65px;padding-bottom:65px}}
@media(prefers-reduced-motion:reduce){.vehicle-card,.destination-card img{transition:none!important}}
'''
        css_path.write_text(css, encoding="utf-8")
        docs = """# SAAFAAR Aether upgrade — 4 November 2026

## What changed
- Retained the independent PHP/MySQL booking, authentication, UPI and audit implementation from the pre-launch source.
- Enhanced the Aether design: floating glass navigation, cinematic dark hero, polished cards, image-led editorial and responsive mobile improvements.
- Replaced clearly marked abstract home artwork with images retrieved from the existing **SAAFAAR** public website.
- Bundled the live SAAFAAR logo for light and dark backgrounds, Meghalaya images, Arunachal mountains, Assam wildlife, and the social-share JPEG.
- Updated the homepage H1 to include the primary Guwahati self-drive rental intent.
- Improved homepage preload and Open Graph/Twitter image content-type consistency.
- Upgraded Meghalaya/Arunachal cards and related guide heroes with destination-appropriate, locally bundled photos.

## What is still blocked
- The supplied vehicle ZIP/inventory has not been independently approved for SAAFAAR. No vehicle photographs, fake listings, rates, deposits or availability have been fabricated.
- Hornbill/Mago/Bumla-specific photography has not been provided or verified; these areas retain clearly bounded visual placeholders.
- Rental/driver policies, live receiving-account verification, legal text and SEO editorial approvals are not supplied. The application deliberately keeps its staging/noindex/booking/payment safeguards in place.
- This repackaging changes visual assets and markup; it does NOT publish to https://saafaar.in, change DNS, verify Search Console, test cPanel PHP or run production payments.

## Deployment
Read docs/INSTALLATION.md and docs/DEPLOYMENT.md inside this ZIP. Set the site's **document root to the ZIP's public directory**. Keep private app/config/storage directories as siblings outside the web root; import the independent database, configure credentials privately and run the hosting preflight. Do not drop only public/index.php into public_html.

Back up the currently deployed website before replacing files. Keep live config/config.php, private media originals, databases and session data. This package is a new-install source package, not a destructive in-place upgrade script.

If all non-home routes still return 404 after deployment, check the actual Apache rewrite rules, document root and PHP error log. Do not disable private-directory protections to hide the routing problem.

## Testing
This build script checks the base ZIP CRC, image signature/size, patch anchors, new ZIP CRC and required file presence. PHP 8.2 runtime, MySQL, browser screenshots and live Apache routing are NOT validated by the packaging job. The original archive contains an earlier local test report; those results do not automatically validate the new revision.
"""
        write_text(root, "docs/AETHER-REVISION.md", docs)
        oldreadme = (root / "README.md").read_text(encoding="utf-8")
        (root / "README.md").write_text("# Aether premium revision\n\nSee docs/AETHER-REVISION.md for this revision's exact scope and unverified dependencies.\n\n" + oldreadme, encoding="utf-8")
        oldseo = (root / "docs/SEO-AUDIT.md").read_text(encoding="utf-8")
        (root / "docs/SEO-AUDIT.md").write_text(oldseo + "\n\n## Aether revision\nHomepage now uses a descriptive Guwahati self-drive H1, approved SAAFAAR site image assets, and a JPEG social preview served with a matching extension. Review all travel-guide claims and approval gates before permitting indexation.\n", encoding="utf-8")

        required = ("public/index.php","app/front-controller.php","app/layout.php","app/content.php",
                    "public/.htaccess","public/assets/css/style.css","database/schema.sql",
                    "docs/AETHER-REVISION.md","docs/INSTALLATION.md","docs/DEPLOYMENT.md")
        for name in required:
            if not (root / name).is_file():
                raise RuntimeError("Required file missing: " + name)
        assert "meghalaya-waterfall-1600.webp" in front
        assert "twitter:image" in layout
        assert "function destination_cards():void" in layout
        assert "Self Drive Car Rental" in front
        assert "SAAFAAR" in front
        with zipfile.ZipFile(TARGET, "w", zipfile.ZIP_DEFLATED, compresslevel=7) as dest:
            for file in sorted(root.rglob("*")):
                if file.is_file():
                    dest.write(file, str(pathlib.Path(PREFIX) / file.relative_to(root)).replace("\\", "/"))
        with zipfile.ZipFile(TARGET) as result:
            bad = result.testzip()
            if bad:
                raise RuntimeError("New ZIP CRC failure: " + bad)
            print("Built",TARGET.name,"files",len(result.namelist()),"size",TARGET.stat().st_size)
        digest = hashlib.sha256(TARGET.read_bytes()).hexdigest()
        (REPO / (TARGET.name + ".sha256")).write_text(digest + "  " + TARGET.name + "\n", encoding="utf-8")
        print("SHA256", digest)
        print("IMPORTANT: not deployed, staged until owner approvals and live host testing")

if __name__ == "__main__":
    build()
