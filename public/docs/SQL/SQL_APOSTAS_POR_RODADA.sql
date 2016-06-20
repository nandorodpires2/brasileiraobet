/* APOSTAS POR RODADA */
select	p.partida_rodada,
			count(*) as apostas
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
group by p.partida_rodada