export interface Stat { value: string, label: string, icon: string }
export interface ServiceCard { title: string, text: string, image: string, to: string }

export const stats: Stat[] = [
  { value: '2014', label: 'год основания', icon: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>' },
  { value: '49', label: 'сотрудников', icon: '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-4 3-6 7-6s7 2 7 6M16 4.5a3.5 3.5 0 010 7M18 14c2.5.6 4 2.4 4 6"/>' },
  { value: '25+', label: 'лет опыта в морской отрасли', icon: '<circle cx="12" cy="9" r="6"/><path d="M8.5 14l-1.5 7 5-3 5 3-1.5-7"/>' },
  { value: '3', label: 'офиса: Владивосток, Москва, Находка', icon: '<path d="M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>' },
  { value: '4', label: 'компании в группе', icon: '<circle cx="12" cy="5" r="2"/><path d="M12 7v14M5 13c0 4 3 8 7 8s7-4 7-8M3 13h4M17 13h4M8 10h8"/>' },
]

export const serviceCards: ServiceCard[] = [
  { title: 'Мультимодальные перевозки', text: 'Море, железная дорога и авто под одним договором или отдельными звеньями.', image: '/img/train.jpg', to: '/services/multimodal' },
  { title: 'Каботажные перевозки', text: 'Владивосток — Магадан, Корсаков, Петропавловск-Камчатский, Анадырь.', image: '/img/cabotage.jpg', to: '/services/multimodal' },
  { title: 'Проектные перевозки', text: 'Негабарит, тяжеловесы, целые заводы и объекты нефтегаза.', image: '/img/p-pipe.jpg', to: '/services/multimodal' },
  { title: 'Агентирование', text: 'Судовое и грузовое агентирование в портах Дальнего Востока.', image: '/img/port.jpg', to: '/services/multimodal' },
]

export const projectGallery = [
  { src: '/img/p-pipe.jpg', alt: 'Трубные колена на железнодорожной платформе' },
  { src: '/img/p-dozer.jpg', alt: 'Бульдозер на платформе' },
  { src: '/img/p-crane.jpg', alt: 'Погрузка негабаритного оборудования' },
  { src: '/img/p-carbox.jpg', alt: 'Автомобиль в рамной конструкции для контейнера' },
]

export const partners = ['MSC', 'Transcontainer', 'Dong Young', 'Eurosib', 'SUNSCO', 'Tenpoint', 'ISACO']
