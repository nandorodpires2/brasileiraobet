/* SALDO APOSTAS */
select	count(*) as apostas,
			sum(p.partida_valor) as montante,
			ifnull(sum(a.aposta_vencedora_valor), 0) as premios,
			sum(p.partida_valor) - sum(a.aposta_vencedora_valor) as saldo
from		aposta a
			inner join partida p on a.partida_id = p.partida_id

