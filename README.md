# ComputerShop

Vežba 1

Realizovati prodavnicu računara.
Prodavnica računara na lageru ima artikle. Svaki artikal ima svoj serijski broj, proizvođača, model, cenu i trenutno stanje na lageru (broj).
Postoje sledeći artikli:
RAM, koji je dodatno opisan kapacitetom i frekvencijom.
CPU, koji je dodatno opisan brojem jezgara i frekvencijom.
HDD, koji je dodatno opisan kapacitetom.
GPU, koji je dodatno opisan frekvencijom.
Potrebno je u prodavnicu dodati artikle koji su kreirani u listu stvari koje prodavnica prodaje. 

Vežba 2

Proširiti prvu vezbu tako da prodavnica računara ima i polje koje predstavlja balans prodavnice (zaradu).
Proširiti prodavnicu računara tako da je moguće prodati artikal (iskoristiti polimorfizam). U tom slučaju se smanjuje njegovo stanje na lageru i povećava zarada prodavnice. Ispisati odgovarajuću poruku ako artikla nema na lageru.

Vežba 3

Napraviti interfejs koji reprezentuje artikle koji se mogu pozajmiti (i vratiti sa pozajmice).
Realizovati metodu za pozajmljivanje artikala u okviru prodavnice računara. Kada se artikal pozajmi, stanje na lageru se smanjuje, a balans se uveća za ¼ cene artikla (pozajmica je jeftinija). Vraćanjem artikla sa pozajmice stanje na lageru se uvecava. Ukoliko artikal nije moguće pozajmiti, prikazati odgovarajuću poruku.
