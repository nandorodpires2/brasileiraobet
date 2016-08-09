/* MEDIA APOSTAS RODADA */
select	p.partida_rodada,
			count(*) as apostas
from		aposta a 
			inner join partida p on a.partida_id = p.partida_id
where		p.partida_serie = 1
group by p.partida_rodada
order by p.partida_rodada desc