<script setup lang="ts">
import { imageSizes } from '@shared/config'
</script>

<template>
  <div
    class="map"
    role="img"
    aria-label="Схема основных маршрутов группы компаний: Китай, Корея, Япония — Владивосток, Восточный, Находка — Магадан, Корсаков, Петропавловск-Камчатский, Анадырь; железная дорога на Москву, Новосибирск, Екатеринбург"
  >
    <NuxtImg
      class="map__bg"
      src="/img/map-russia.png"
      alt=""
      width="1440"
      height="843"
      :sizes="imageSizes.half"
      format="webp"
      loading="lazy"
    />
    <svg viewBox="0 0 1440 843" aria-hidden="true">
<path class="route route--sea" d="M1300 600 C1240 690 1180 700 1130 650"/>
<path class="route route--sea" d="M1300 600 C1360 520 1340 430 1270 330"/>
<path class="route route--sea" d="M1300 600 C1370 540 1390 470 1360 410"/>
<path class="route route--sea" d="M1300 600 C1380 440 1400 300 1390 210"/>
<path class="route route--sea" d="M1300 600 C1340 640 1330 700 1300 760"/>
<path class="route route--rail" d="M1300 600 C1000 560 700 520 520 470 S440 520 390 520"/>
<circle class="pt pt--hub" cx="1300" cy="600" r="13"/>
<circle class="pt" cx="1270" cy="330" r="10"/><circle class="pt" cx="1360" cy="410" r="10"/><circle class="pt" cx="1390" cy="210" r="10"/><circle class="pt" cx="1360" cy="520" r="10"/>
<circle class="pt" cx="1130" cy="650" r="10"/><circle class="pt" cx="1300" cy="760" r="10"/>
<circle class="pt" cx="760" cy="515" r="9"/><circle class="pt" cx="590" cy="495" r="9"/><circle class="pt pt--hub" cx="390" cy="520" r="13"/>
<text x="1280" y="640" text-anchor="end">Владивосток · Восточный · Находка</text>
<text x="1160" y="318" text-anchor="end">Магадан</text><text x="1340" y="395" text-anchor="end">Петропавловск-К.</text><text x="1370" y="192" text-anchor="end">Анадырь</text>
<text x="1335" y="545" text-anchor="end">Корсаков</text><text x="1115" y="690" text-anchor="end">Китай · Корея</text><text x="1290" y="795" text-anchor="end">Япония</text>
<text x="760" y="550" text-anchor="middle">Новосибирск</text><text x="590" y="527" text-anchor="middle">Екатеринбург</text><text x="390" y="558" text-anchor="middle">Москва</text>
</svg>
  </div>
</template>
