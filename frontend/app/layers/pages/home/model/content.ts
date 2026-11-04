export interface ServiceCard { title: string, text: string, image: string, to: string }

export const serviceCards: ServiceCard[] = [
  { title: 'Мультимодальные перевозки', text: 'Море, железная дорога и авто под одним договором или отдельными звеньями.', image: '/img/train.jpg', to: '/services/multimodal' },
  { title: 'Каботажные перевозки', text: 'Владивосток — Магадан, Корсаков, Петропавловск-Камчатский, Анадырь.', image: '/img/cabotage.jpg', to: '/services/multimodal' },
  { title: 'Проектные перевозки', text: 'Негабарит, тяжеловесы, целые заводы и объекты нефтегаза.', image: '/img/p-pipe.jpg', to: '/services/multimodal' },
  { title: 'Агентирование', text: 'Судовое и грузовое агентирование в портах Дальнего Востока.', image: '/img/port.jpg', to: '/services/multimodal' },
]

