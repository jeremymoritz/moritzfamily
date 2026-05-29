// Regenerates bbb-cast.json from bbb-track-numbers.csv.
// auditionId is looked up by name from gtip-audition-names.csv.
import { readFileSync, writeFileSync } from 'node:fs';

const norm = s => s.trim().toLowerCase();

// Minimal CSV line parser handling quoted fields with embedded quotes/commas.
function parseLine(line) {
  const out = [];
  let cur = '';
  let inQ = false;
  for (let i = 0; i < line.length; i++) {
    const c = line[i];
    if (inQ) {
      if (c === '"') {
        if (line[i + 1] === '"') { cur += '"'; i++; }
        else inQ = false;
      } else cur += c;
    } else if (c === '"') inQ = true;
    else if (c === ',') { out.push(cur); cur = ''; }
    else cur += c;
  }
  out.push(cur);
  return out;
}

const rows = s =>
  s.replace(/^﻿/, '').split(/\r?\n/).filter(l => l.trim() !== '').map(parseLine);

// audition name map: "lastname|firstname" -> id
const audMap = new Map();
for (const [id, last, first] of rows(readFileSync('gtip-audition-names.csv', 'utf8'))) {
  audMap.set(`${norm(last)}|${norm(first)}`, Number(id));
}

function heightToInches(h) {
  const m = h.match(/(\d+)\s*'\s*(\d+)?/);
  if (!m) return null;
  return Number(m[1]) * 12 + Number(m[2] || 0);
}

const csv = rows(readFileSync('bbb-track-numbers.csv', 'utf8'));
csv.shift(); // header

const cast = [];
const missing = [];
for (const [first, last, role, groups, gender, height, track] of csv) {
  const key = `${norm(last)}|${norm(first)}`;
  const auditionId = audMap.get(key);
  if (auditionId === undefined) missing.push(`${first} ${last}`);
  cast.push({
    trackNumber: Number(track),
    auditionId: auditionId ?? null,
    firstName: first.trim(),
    lastName: last.trim(),
    gender: gender.trim(),
    height: heightToInches(height),
    roles: role.trim() ? role.split('/').map(r => r.trim()) : [],
    groups: groups.trim() ? groups.split('/').map(g => g.trim()) : []
  });
}

cast.sort((a, b) => a.trackNumber - b.trackNumber);

const data = { show: 'Bye Bye Birdie', date: '2026-08-07', cast };
// Match existing formatting: tabs, roles/groups arrays inline.
let json = JSON.stringify(data, null, '\t')
  .replace(/\[\n\t+\]/g, '[]')
  .replace(/\[\n\t+("(?:[^"\\]|\\.)*"(?:,\n\t+"(?:[^"\\]|\\.)*")*)\n\t+\]/g,
    (_, inner) => '[' + inner.replace(/\n\t+/g, ' ') + ']');

writeFileSync('bbb-cast.json', json + '\n');
console.log(`Wrote ${cast.length} cast members.`);
if (missing.length) console.log('NO auditionId match for:', missing.join(', '));
