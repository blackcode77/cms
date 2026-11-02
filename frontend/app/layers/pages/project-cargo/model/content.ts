export interface CargoExample { title: string, text: string, image: string, width: number, height: number }

export const sampleRoutes: string[] = [
  'Сахалин → ОАЭ',
  'Китай → Тобольск (СИБУР)',
  'Трансформаторы 140–180 т → Магадан',
  'Пусан → Москва',
  'Корея → Кострома (через Санкт-Петербург)',
  'Эстония, Рига → Кабул; Афганистан → Бишкек',
]

export const cargoExamples: CargoExample[] = [
  { title: 'Трубные колена на платформе', text: 'Крупногабаритные трубные изделия: схема крепления и железнодорожная доставка.', image: '/img/p-pipe.jpg', width: 460, height: 322 },
  { title: 'Бульдозеры и спецтехника', text: 'Перевалка гусеничной техники с судна на платформу.', image: '/img/p-dozer.jpg', width: 795, height: 508 },
  { title: 'Рулонная сталь', text: 'Перевозка рулонов в полувагонах с индивидуальной схемой крепления.', image: '/img/p-coil.jpg', width: 644, height: 358 },
  { title: 'Негабаритное оборудование', text: 'Погрузка многотонного оборудования с использованием кранов.', image: '/img/p-crane.jpg', width: 479, height: 321 },
  { title: 'Ёмкости и цистерны', text: 'Крупные ёмкости на платформах, транспортировка с сопровождением.', image: '/img/p-tank.jpg', width: 594, height: 369 },
  { title: 'Тяжеловесные грузы в полувагонах', text: 'Перегрузка и доставка тяжеловесов по железной дороге.', image: '/img/p-rail.jpg', width: 429, height: 326 },
  { title: 'Автомобили', text: 'Партионная перевозка автомобилей: Корея — Россия.', image: '/img/p-cars.jpg', width: 646, height: 416 },
  { title: 'Авто в контейнере', text: 'Технология: 3–4 автомобиля в 40HC, погрузка и крепление вне контейнера.', image: '/img/p-carbox.jpg', width: 563, height: 390 },
]
