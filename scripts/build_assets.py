#!/usr/bin/env python3
"""Fetch original photos already on saafaar.in and create branded icons, no external Python dependencies."""
import os, math, struct, urllib.request, zlib
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
SOURCES={
"meghalaya-waterfall.webp":"https://saafaar.in/assets/img/meghalaya-waterfall-1600.webp",
"meghalaya-hills.webp":"https://saafaar.in/assets/img/meghalaya-hills-waterfalls-1600.webp",
"arunachal-mountains.webp":"https://saafaar.in/assets/img/arunachal-mountain-gateway-1024.webp",
"kaziranga-rhino.webp":"https://saafaar.in/assets/img/kaziranga-rhino-1600.webp",
"saafaar-logo.webp":"https://saafaar.in/assets/logo/saafaar-logo-optimized.webp"}
def fetch():
 d=ROOT/"assets"/"img";d.mkdir(parents=True,exist_ok=True)
 for fname,url in SOURCES.items():
  p=d/fname
  if p.exists() and p.stat().st_size>1000:continue
  req=urllib.request.Request(url,headers={"User-Agent":"SAAFAAR-Asset-Bundler/1.0"})
  with urllib.request.urlopen(req,timeout=40) as r:b=r.read()
  if len(b)<1000 or b[:4]!=b"RIFF" or b[8:12]!=b"WEBP":raise RuntimeError("Invalid WEBP original: "+url)
  p.write_bytes(b);print("Saved",fname,len(b))
def png(n):
 pixels=bytearray(n*n*4)
 glyph=["11111","10000","10000","11111","00001","00001","11111"]
 for y in range(n):
  for x in range(n):
   rx=min(x,n-1-x);ry=min(y,n-1-y)
   inside=(rx>=n*.17 or ry>=n*.17 or (rx-n*.17)**2+(ry-n*.17)**2<=(n*.17)**2)
   color=(17,19,24,255) if inside else (0,0,0,0)
   curve=n*(.24+.075*((x/n-.5)**2))
   if .13*n<x<.87*n and abs(y-curve)<n*.025:color=(220,38,38,255)
   box=n*.078;left=n*.305;top=n*.40
   row=int((y-top)/box);col=int((x-left)/box)
   if 0<=row<7 and 0<=col<5 and glyph[row][col]=="1":color=(255,255,255,255)
   pixels[(y*n+x)*4:(y*n+x)*4+4]=bytes(color)
 def chunk(t,b):return struct.pack(">I",len(b))+t+b+struct.pack(">I",zlib.crc32(t+b)&0xffffffff)
 raw=b"".join(b"\0"+pixels[y*n*4:(y+1)*n*4] for y in range(n))
 return b"\x89PNG\r\n\x1a\n"+chunk(b"IHDR",struct.pack(">IIBBBBB",n,n,8,6,0,0,0))+chunk(b"IDAT",zlib.compress(raw,9))+chunk(b"IEND",b"")
def icons():
 d=ROOT/"assets"/"icons";d.mkdir(parents=True,exist_ok=True)
 for n,fn in [(16,"favicon-16x16.png"),(32,"favicon-32x32.png"),(192,"icon-192.png"),(512,"icon-512.png")]:
  (d/fn).write_bytes(png(n))
 (ROOT/"apple-touch-icon.png").write_bytes(png(180))
 p=png(32);(ROOT/"favicon.ico").write_bytes(struct.pack("<HHH",0,1,1)+struct.pack("<BBBBHHII",32,32,0,0,1,32,len(p),22)+p)
if __name__=="__main__":fetch();icons()
