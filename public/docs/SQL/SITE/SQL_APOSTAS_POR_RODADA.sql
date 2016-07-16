/* APOSTAS POR RODADA */
select	p.partida_rodada,
			count(*) as apostas,
			sum(p.partida_valor) as montante
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
where		p.partida_serie = 1
group by p.partida_rodada
order by p.partida_rodada desc