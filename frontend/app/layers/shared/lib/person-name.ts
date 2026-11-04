export interface PersonName {
  first_name: string
  last_name: string
  middle_name: string
}

/** «Фамилия Имя Отчество», skipping unknown parts; empty when nothing is known. */
export function fullName(person: PersonName): string {
  return [person.last_name, person.first_name, person.middle_name].filter(part => part !== '').join(' ')
}
